<?php

namespace App\Services;

use App\Events\SeatCancellationNotice;
use App\Events\SeatReleased;
use App\Events\SeatReserved;
use App\Exceptions\ReservationAlreadyProcessedException;
use App\Models\Reservation;
use App\Models\ReservationLog;
use App\Models\Screening;
use App\Models\Seat;
use App\Models\SeatHold;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function seatPriceCents(Screening $screening, Seat $seat): int
    {
        return $screening->base_price + $seat->price_modifier;
    }

    /**
     * @param  iterable<int, Seat>  $seats
     */
    public function totalPriceCents(Screening $screening, iterable $seats): int
    {
        $total = 0;

        foreach ($seats as $seat) {
            $total += $this->seatPriceCents($screening, $seat);
        }

        return $total;
    }

    public function finalize(string $holdToken, string $paymentStatus): ?Reservation
    {
        $user = Auth::user();

        if ($user === null) {
            throw new AuthorizationException;
        }

        if ($holdToken === '') {
            throw ValidationException::withMessages(['hold_token' => 'Hold token is required.']);
        }

        return DB::transaction(function () use ($holdToken, $paymentStatus, $user) {
            $supportsRowLocking = in_array(DB::getDriverName(), ['pgsql', 'mysql', 'mariadb'], true);
            $existingReservation = Reservation::query()->where('hold_token', $holdToken)->first();

            if ($existingReservation !== null) {
                throw new ReservationAlreadyProcessedException;
            }

            $holdsQuery = SeatHold::query()->where('hold_token', $holdToken);

            if ($supportsRowLocking) {
                $holdsQuery->lockForUpdate();
            }

            $holds = $holdsQuery->get();

            if ($holds->isEmpty()) {
                throw ValidationException::withMessages(['hold_token' => 'Invalid hold token.']);
            }

            $this->assertHoldOwnership($holds, $user->getKey());
            $this->assertHoldsNotExpired($holds);

            $screeningId = (int) $holds->first()->screening_id;

            $this->assertSingleScreening($holds, $screeningId);

            $screeningQuery = Screening::query()->whereKey($screeningId);

            if ($supportsRowLocking) {
                $screeningQuery->lockForUpdate();
            }

            $screening = $screeningQuery->firstOrFail();

            $seatIds = $holds->pluck('seat_id')->map(fn ($id) => (int) $id)->all();

            $seatsQuery = Seat::query()->whereIn('id', $seatIds);

            if ($supportsRowLocking) {
                $seatsQuery->lockForUpdate();
            }

            /** @var Collection<int, Seat> $seats */
            $seats = $seatsQuery->get();

            if ($seats->count() !== count($seatIds)) {
                throw ValidationException::withMessages(['hold_token' => 'One or more seats are invalid.']);
            }

            foreach ($seats as $seat) {
                if ($seat->theater_id !== $screening->theater_id) {
                    throw ValidationException::withMessages(['hold_token' => 'All seats must belong to the screening theater.']);
                }
            }

            $this->assertRedisHoldsExist($holds, $screeningId, $user->getKey());
            $this->assertSeatsNotAlreadyReserved($screeningId, $seatIds, $supportsRowLocking);

            if ($paymentStatus === 'fail') {
                $this->deleteRedisHolds($screeningId, $seatIds);
                SeatHold::query()->where('hold_token', $holdToken)->delete();

                DB::afterCommit(function () use ($screeningId, $seatIds, $user) {
                    foreach ($seatIds as $seatId) {
                        event(new SeatReleased(
                            screeningId: $screeningId,
                            seatId: $seatId,
                            userId: $user->getKey(),
                        ));
                    }
                });

                return null;
            }

            if ($paymentStatus !== 'success') {
                throw ValidationException::withMessages(['payment_status' => 'Invalid payment status.']);
            }

            $reservation = Reservation::query()->create([
                'user_id' => $user->getKey(),
                'screening_id' => $screeningId,
                'hold_token' => $holdToken,
                'total_price' => $this->totalPriceCents($screening, $seats),
                'status' => 'confirmed',
                'payment_reference' => 'mock_'.Str::uuid()->toString(),
                'confirmed_at' => CarbonImmutable::now(),
            ]);

            $pivotData = [];

            foreach ($seats as $seat) {
                $pivotData[$seat->getKey()] = [
                    'price' => $this->seatPriceCents($screening, $seat),
                ];
            }

            $reservation->seats()->attach($pivotData);

            $this->deleteRedisHolds($screeningId, $seatIds);
            SeatHold::query()->where('hold_token', $holdToken)->delete();

            DB::afterCommit(function () use ($screeningId, $seatIds, $reservation, $user) {
                foreach ($seatIds as $seatId) {
                    event(new SeatReserved(
                        screeningId: $screeningId,
                        seatId: $seatId,
                        reservationId: $reservation->getKey(),
                        userId: $user->getKey(),
                    ));
                }
            });

            return $reservation;
        });
    }

    /**
     * Cancel a reservation and release the associated seats.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function cancel(Reservation $reservation): array
    {
        $user = Auth::user();

        if ($user === null) {
            throw new AuthorizationException;
        }

        // Check ownership
        if ((int) $reservation->user_id !== (int) $user->getKey()) {
            throw new AuthorizationException;
        }

        return DB::transaction(function () use ($reservation, $user) {
            $supportsRowLocking = in_array(DB::getDriverName(), ['pgsql', 'mysql', 'mariadb'], true);

            // Lock the reservation for update
            $reservationQuery = Reservation::query()->whereKey($reservation->getKey());

            if ($supportsRowLocking) {
                $reservationQuery->lockForUpdate();
            }

            $lockedReservation = $reservationQuery->firstOrFail();

            // Validate cancellation eligibility
            $this->assertReservationEligibleForCancellation($lockedReservation);

            // Lock the screening for consistency
            $screeningQuery = Screening::query()->whereKey($lockedReservation->screening_id);

            if ($supportsRowLocking) {
                $screeningQuery->lockForUpdate();
            }

            $screening = $screeningQuery->firstOrFail();

            // Get and lock the seats
            $seatIds = $lockedReservation->seats->pluck('id')->map(fn ($id) => (int) $id)->all();

            $seatsQuery = Seat::query()->whereIn('id', $seatIds);

            if ($supportsRowLocking) {
                $seatsQuery->lockForUpdate();
            }

            $seats = $seatsQuery->get();

            // Release seats by removing from pivot table
            $lockedReservation->seats()->detach();

            // Update reservation status
            $lockedReservation->update([
                'status' => 'cancelled',
                'cancelled_at' => CarbonImmutable::now(),
            ]);

            // Log the cancellation
            ReservationLog::query()->create([
                'reservation_id' => $lockedReservation->getKey(),
                'user_id' => $user->getKey(),
                'action' => 'cancelled',
                'details' => [
                    'screening_id' => $screening->getKey(),
                    'seat_ids' => $seatIds,
                    'total_price' => $lockedReservation->total_price,
                ],
            ]);

            DB::afterCommit(function () use ($screening, $seatIds, $user) {
                // Fire SeatReleased events for each seat
                foreach ($seatIds as $seatId) {
                    event(new SeatReleased(
                        screeningId: $screening->getKey(),
                        seatId: $seatId,
                        userId: $user->getKey(),
                    ));
                }

                // Fire cancellation notice event
                event(new SeatCancellationNotice(
                    screeningId: $screening->getKey(),
                    seatIds: $seatIds,
                ));
            });

            return [
                'reservation' => $lockedReservation->fresh(['seats', 'screening']),
                'released_seat_ids' => $seatIds,
                'screening' => $screening,
            ];
        });
    }

    /**
     * @param  Collection<int, SeatHold>  $holds
     */
    private function assertHoldOwnership(Collection $holds, int $userId): void
    {
        $isOwner = $holds->every(fn (SeatHold $hold) => (int) $hold->user_id === $userId);

        if (! $isOwner) {
            throw new AuthorizationException;
        }
    }

    /**
     * @param  Collection<int, SeatHold>  $holds
     */
    private function assertHoldsNotExpired(Collection $holds): void
    {
        $now = CarbonImmutable::now();

        $hasExpired = $holds->contains(fn (SeatHold $hold) => CarbonImmutable::parse($hold->expires_at)->lt($now));

        if ($hasExpired) {
            throw ValidationException::withMessages(['hold_token' => 'Hold has expired.']);
        }
    }

    /**
     * @param  Collection<int, SeatHold>  $holds
     */
    private function assertSingleScreening(Collection $holds, int $screeningId): void
    {
        $single = $holds->every(fn (SeatHold $hold) => (int) $hold->screening_id === $screeningId);

        if (! $single) {
            throw ValidationException::withMessages(['hold_token' => 'Hold token must belong to a single screening.']);
        }
    }

    /**
     * @param  Collection<int, SeatHold>  $holds
     */
    private function assertRedisHoldsExist(Collection $holds, int $screeningId, int $userId): void
    {
        foreach ($holds as $hold) {
            $key = $this->holdKey($screeningId, (int) $hold->seat_id);
            $value = Redis::command('GET', [$key]);

            if (! is_string($value) || $value === '') {
                throw ValidationException::withMessages(['hold_token' => 'Redis hold is missing or expired.']);
            }

            $payload = json_decode($value, true, flags: JSON_THROW_ON_ERROR);

            if (! is_array($payload)) {
                throw ValidationException::withMessages(['hold_token' => 'Redis hold is invalid.']);
            }

            if ((int) ($payload['user_id'] ?? 0) !== $userId) {
                throw new AuthorizationException;
            }

            if ((string) ($payload['hold_token'] ?? '') !== (string) $hold->hold_token) {
                throw ValidationException::withMessages(['hold_token' => 'Redis hold token mismatch.']);
            }
        }
    }

    /**
     * @param  array<int, int>  $seatIds
     */
    private function assertSeatsNotAlreadyReserved(int $screeningId, array $seatIds, bool $supportsRowLocking): void
    {
        /** @var Builder $query */
        $query = DB::table('reservation_seat')
            ->join('reservations', 'reservations.id', '=', 'reservation_seat.reservation_id')
            ->where('reservations.screening_id', $screeningId)
            ->where('reservations.status', 'confirmed')
            ->whereIn('reservation_seat.seat_id', $seatIds);

        if ($supportsRowLocking) {
            $query->lockForUpdate();
        }

        $reservedSeatIds = $query->pluck('reservation_seat.seat_id')->all();

        if (count($reservedSeatIds) > 0) {
            throw ValidationException::withMessages(['hold_token' => 'One or more seats are already reserved.']);
        }
    }

    /**
     * @param  array<int, int>  $seatIds
     */
    private function deleteRedisHolds(int $screeningId, array $seatIds): void
    {
        $keys = [];

        foreach ($seatIds as $seatId) {
            $keys[] = $this->holdKey($screeningId, $seatId);
        }

        if ($keys === []) {
            return;
        }

        Redis::command('DEL', $keys);
    }

    private function holdKey(int $screeningId, int $seatId): string
    {
        return "hold:screening:{$screeningId}:seat:{$seatId}";
    }

    /**
     * Assert that a reservation is eligible for cancellation.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function assertReservationEligibleForCancellation(Reservation $reservation): void
    {
        // Must be confirmed status
        if ($reservation->status !== 'confirmed') {
            throw ValidationException::withMessages([
                'reservation' => 'Only confirmed reservations can be cancelled.',
            ]);
        }

        // Must not already be cancelled
        if ($reservation->status === 'cancelled') {
            throw ValidationException::withMessages([
                'reservation' => 'Reservation is already cancelled.',
            ]);
        }

        // Must not be expired
        if ($reservation->status === 'expired') {
            throw ValidationException::withMessages([
                'reservation' => 'Expired reservations cannot be cancelled.',
            ]);
        }

        // Check if screening starts within 60 minutes (configurable)
        $screening = $reservation->screening;

        if ($screening === null) {
            throw ValidationException::withMessages([
                'reservation' => 'Reservation screening not found.',
            ]);
        }

        $minutesUntilScreening = CarbonImmutable::now()->diffInMinutes($screening->start_time, false);

        if ($minutesUntilScreening <= 60) {
            throw ValidationException::withMessages([
                'reservation' => 'Reservations cannot be cancelled within 60 minutes of the screening start time.',
            ]);
        }
    }
}
