<?php

namespace App\Services;

use App\Models\SeatHold;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class SeatHoldService
{
    public const int HOLD_TTL_SECONDS = 600;

    /**
     * @return array{hold_token: string, expires_at: CarbonImmutable}|false
     */
    public function holdSeat(int $screeningId, int $seatId, int $userId, ?string $holdToken = null): array|false
    {
        if ($holdToken === null) {
            $existingHoldToken = SeatHold::query()
                ->where('user_id', $userId)
                ->where('screening_id', $screeningId)
                ->where('expires_at', '>=', CarbonImmutable::now())
                ->value('hold_token');

            $holdToken = is_string($existingHoldToken) && $existingHoldToken !== ''
                ? $existingHoldToken
                : Str::random(40);
        }

        $expiresAt = CarbonImmutable::now()->addSeconds(self::HOLD_TTL_SECONDS);

        $key = $this->holdKey($screeningId, $seatId);
        $value = json_encode([
            'user_id' => $userId,
            'hold_token' => $holdToken,
            'expires_at' => $expiresAt->toIso8601String(),
        ], JSON_THROW_ON_ERROR);

        // $result = Redis::command('SET', [$key, $value, 'EX', self::HOLD_TTL_SECONDS, 'NX']);
        $result = Redis::set($key, $value, 'EX', self::HOLD_TTL_SECONDS, 'NX');

        if ($result !== 'OK' && $result !== true) {
            return false;
        }

        SeatHold::query()
            ->where('screening_id', $screeningId)
            ->where('seat_id', $seatId)
            ->delete();

        try {
            SeatHold::query()->create([
                'user_id' => $userId,
                'screening_id' => $screeningId,
                'seat_id' => $seatId,
                'hold_token' => $holdToken,
                'expires_at' => $expiresAt,
                'created_at' => CarbonImmutable::now(),
            ]);
        } catch (\Throwable $e) {
            Redis::command('DEL', [$key]);

            throw $e;
        }

        return [
            'hold_token' => $holdToken,
            'expires_at' => $expiresAt,
        ];
    }

    public function releaseSeat(int $screeningId, int $seatId, int $userId): bool
    {
        $key = $this->holdKey($screeningId, $seatId);
        $hold = $this->getHold($screeningId, $seatId);

        if ($hold === null) {
            SeatHold::query()
                ->where('screening_id', $screeningId)
                ->where('seat_id', $seatId)
                ->delete();

            return false;
        }

        if ((int) $hold['user_id'] !== $userId) {
            throw new AuthorizationException;
        }

        Redis::command('DEL', [$key]);

        SeatHold::query()
            ->where('screening_id', $screeningId)
            ->where('seat_id', $seatId)
            ->delete();

        return true;
    }

    /**
     * @return array{user_id: int, hold_token: string, expires_at: string}|null
     */
    public function getHold(int $screeningId, int $seatId): ?array
    {
        $key = $this->holdKey($screeningId, $seatId);

        $value = Redis::command('GET', [$key]);

        if (! is_string($value) || $value === '') {
            SeatHold::query()
                ->where('screening_id', $screeningId)
                ->where('seat_id', $seatId)
                ->delete();

            return null;
        }

        /** @var array{user_id: int, hold_token: string, expires_at: string} $payload */
        $payload = json_decode($value, true, flags: JSON_THROW_ON_ERROR);

        return $payload;
    }

    public function isSeatHeld(int $screeningId, int $seatId): bool
    {
        return $this->getHold($screeningId, $seatId) !== null;
    }

    private function holdKey(int $screeningId, int $seatId): string
    {
        return "hold:screening:{$screeningId}:seat:{$seatId}";
    }
}
