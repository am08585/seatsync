<?php

namespace App\Livewire;

use App\Models\Screening;
use App\Models\Seat;
use App\Services\SeatHoldService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SeatSelection extends Component
{
    public Screening $screening;

    public array $selectedSeats = [];

    public array $seatHolds = [];

    public string $holdToken = '';

    // Performance optimization: Pre-fetched data
    public array $reservedSeats = [];

    public array $heldSeats = [];

    public array $seatModels = [];

    protected SeatHoldService $seatHoldService;

    protected $listeners = [
        'seatReleased' => '$refresh',
        'seatHeld' => '$refresh',
    ];

    public function mount(Screening $screening): void
    {
        $this->screening = $screening->load(['theater', 'movie', 'reservations', 'reservations.seats']);
        $this->preloadSeatData();
        $this->loadSeatMap();
    }

    public function boot(SeatHoldService $seatHoldService): void
    {
        $this->seatHoldService = $seatHoldService;
    }

    /**
     * Pre-fetch all seat data to eliminate N+1 queries
     */
    public function preloadSeatData(): void
    {
        // Fetch all seats for this theater once
        $this->seatModels = Seat::where('theater_id', $this->screening->theater_id)
            ->get()
            ->keyBy('id')
            ->toArray();

        // Fetch all reserved seats for this screening in one query
        $this->reservedSeats = $this->screening->reservations()
            ->with('seats')
            ->get()
            ->flatMap(function ($reservation) {
                return $reservation->seats->pluck('id');
            })
            ->unique()
            ->flip()
            ->toArray();

        // Fetch all active seat holds for this screening in one query
        $this->heldSeats = $this->screening->seatHolds()
            ->where('expires_at', '>', now())
            ->pluck('seat_id')
            ->unique()
            ->flip()
            ->toArray();
    }

    public function loadSeatMap(): void
    {
        $this->seatHolds = $this->screening->seatHolds()
            ->with('seat')
            ->get()
            ->keyBy('seat_id')
            ->toArray();

        // Only reset selectedSeats if it's empty (first load)
        if (empty($this->selectedSeats)) {
            $this->selectedSeats = [];
        }

        // Initialize or get existing hold token for this screening
        $user = auth()->user();
        if ($user) {
            $existingHold = $this->screening->seatHolds()
                ->where('user_id', $user->id)
                ->where('expires_at', '>', now()->toDateTimeString())
                ->first();

            $this->holdToken = $existingHold?->hold_token ?? '';

            // If we have a hold token but no selected seats, populate them from existing holds
            if ($this->holdToken && empty($this->selectedSeats)) {
                $userHolds = $this->screening->seatHolds()
                    ->where('user_id', $user->id)
                    ->where('expires_at', '>', now()->toDateTimeString())
                    ->with('seat')
                    ->get();

                $this->selectedSeats = $userHolds->map(function ($hold) {
                    return [
                        'seat_id' => $hold->seat_id,
                        'hold_token' => $hold->hold_token,
                        'expires_at' => $hold->expires_at->toIso8601String(),
                    ];
                })->toArray();
            }
        }

        // Refresh preloaded data to ensure consistency
        $this->preloadSeatData();
    }

    public function getTheaterSeats(): Collection
    {
        return Seat::where('theater_id', $this->screening->theater_id)
            ->orderBy('row')
            ->orderBy('number')
            ->get()
            ->groupBy('row');
    }

    public function getSeatStatus(Seat $seat): string
    {
        // Check if seat is reserved using pre-fetched data
        $isReserved = isset($this->reservedSeats[$seat->id]);

        // Check if seat is held using pre-fetched data
        $isHeld = isset($this->heldSeats[$seat->id]);

        // Check if seat is currently selected by this user
        $isSelected = in_array($seat->id, array_column($this->selectedSeats, 'seat_id'));

        if ($isReserved) {
            return 'reserved';
        } elseif ($isHeld && ! $isSelected) {
            // Seat is held by someone else
            return 'held';
        } elseif ($isHeld && $isSelected) {
            // Seat is held by current user - allow toggling to release
            return 'selected_held';
        }

        return 'available';
    }

    public function getSeatPrice(Seat $seat): float
    {
        $basePrice = $this->screening->base_price / 100; // Convert from cents

        return $basePrice + $seat->price_modifier / 100;
    }

    /**
     * Get seat model from pre-fetched data to avoid database queries
     */
    private function getSeatModel(int $seatId): ?Seat
    {
        if (isset($this->seatModels[$seatId])) {
            return new Seat($this->seatModels[$seatId]);
        }

        return null;
    }

    public function toggleSeatSelection(int $seatId): void
    {
        $seat = $this->getSeatModel($seatId);
        if (! $seat) {
            return;
        }

        $user = auth()->user();
        if (! $user) {
            return;
        }

        $seatStatus = $this->getSeatStatus($seat);

        if (in_array($seatId, array_column($this->selectedSeats, 'seat_id'))) {
            // Release seat
            $this->releaseSeat($seatId);
        } elseif ($seatStatus === 'available') {
            // Hold seat
            $this->holdSeat($seatId);
        } elseif ($seatStatus === 'selected_held') {
            // Release currently held seat
            $this->releaseSeat($seatId);
        }
        // If seat is 'held' by someone else, do nothing
    }

    private function holdSeat(int $seatId): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        try {
            $result = $this->seatHoldService->holdSeat(
                screeningId: $this->screening->id,
                seatId: $seatId,
                userId: $user->id,
                holdToken: $this->holdToken ?: null
            );

            if ($result) {
                $this->selectedSeats[] = [
                    'seat_id' => $seatId,
                    'hold_token' => $result['hold_token'],
                    'expires_at' => $result['expires_at']->toIso8601String(),
                ];
                $this->holdToken = $result['hold_token'];
                $this->refreshSeatHolds(); // Only refresh seat holds, not full loadSeatMap
            }
        } catch (\Exception $e) {
            // Handle seat already held or other errors
            $this->loadSeatMap(); // Refresh to show current state
        }
    }

    private function refreshSeatHolds(): void
    {
        $this->seatHolds = $this->screening->seatHolds()
            ->with('seat')
            ->get()
            ->keyBy('seat_id')
            ->toArray();

        // Also refresh the heldSeats lookup array for consistency
        $this->heldSeats = $this->screening->seatHolds()
            ->where('expires_at', '>', now())
            ->pluck('seat_id')
            ->unique()
            ->flip()
            ->toArray();
    }

    private function releaseSeat(int $seatId): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        try {
            $this->seatHoldService->releaseSeat(
                screeningId: $this->screening->id,
                seatId: $seatId,
                userId: $user->id
            );

            $this->selectedSeats = array_values(array_filter(
                $this->selectedSeats,
                fn ($seat) => $seat['seat_id'] !== $seatId
            ));
            $this->refreshSeatHolds(); // Only refresh seat holds, not full loadSeatMap
        } catch (\Exception $e) {
            // Handle errors
            $this->refreshSeatHolds(); // Only refresh seat holds, not full loadSeatMap
        }
    }

    public function getTotalPrice(): string
    {
        $total = 0;
        foreach ($this->selectedSeats as $selectedSeat) {
            $seat = $this->getSeatModel($selectedSeat['seat_id']);
            if ($seat) {
                $total += $this->getSeatPrice($seat);
            }
        }

        return '$'.number_format($total, 2);
    }

    public function clearSelectedSeats(): void
    {
        // Release all held seats
        foreach ($this->selectedSeats as $selectedSeat) {
            $this->releaseSeat($selectedSeat['seat_id']);
        }
    }

    public function proceedToPayment(): void
    {
        if (empty($this->selectedSeats) || empty($this->holdToken)) {
            return;
        }

        // Store selected seats and hold token in session for payment page
        session([
            'selected_seats' => array_column($this->selectedSeats, 'seat_id'),
            'screening_id' => $this->screening->id,
            'hold_token' => $this->holdToken,
        ]);

        $this->redirectRoute('payment.mock.show', ['hold_token' => $this->holdToken]);
    }

    public function render(): View
    {
        // Ensure data is preloaded on every render
        if (empty($this->seatModels)) {
            $this->preloadSeatData();
        }

        return view('livewire.seat-selection', [
            'screening' => $this->screening,
            'theaterSeats' => $this->getTheaterSeats(),
            'selectedSeats' => array_column($this->selectedSeats, 'seat_id'),
        ]);
    }
}
