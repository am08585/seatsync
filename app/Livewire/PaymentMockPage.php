<?php

namespace App\Livewire;

use App\Models\Screening;
use App\Models\Seat;
use App\Models\SeatHold;
use App\Services\ReservationService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PaymentMockPage extends Component
{
    #[Locked]
    public string $hold_token;

    public function mount(string $hold_token): void
    {
        $this->hold_token = $hold_token;
    }

    public function render(ReservationService $reservationService)
    {
        $user = Auth::user();

        if ($user === null) {
            abort(403);
        }

        /** @var Collection<int, SeatHold> $holds */
        $holds = SeatHold::query()
            ->where('hold_token', $this->hold_token)
            ->orderBy('created_at')
            ->get();

        if ($holds->isEmpty()) {
            abort(404);
        }

        if ($holds->contains(fn (SeatHold $hold) => (int) $hold->user_id !== (int) $user->getKey())) {
            abort(403);
        }

        $screeningId = (int) $holds->first()->screening_id;

        if ($holds->contains(fn (SeatHold $hold) => (int) $hold->screening_id !== $screeningId)) {
            abort(422);
        }

        $isExpired = $holds->contains(fn (SeatHold $hold) => CarbonImmutable::parse($hold->expires_at)->lt(CarbonImmutable::now()));

        $screening = Screening::query()->with(['movie', 'theater'])->findOrFail($screeningId);

        $seatIds = $holds->pluck('seat_id')->map(fn ($id) => (int) $id)->all();

        /** @var Collection<int, Seat> $seats */
        $seats = Seat::query()
            ->whereIn('id', $seatIds)
            ->orderBy('row')
            ->orderBy('number')
            ->get();

        $totalCents = $reservationService->totalPriceCents($screening, $seats);

        return view('livewire.payment-mock-page', [
            'holds' => $holds,
            'screening' => $screening,
            'seats' => $seats,
            'isExpired' => $isExpired,
            'totalCents' => $totalCents,
            'seatPriceCents' => fn (Seat $seat) => $reservationService->seatPriceCents($screening, $seat),
        ])->layout('layouts.app', ['title' => 'Mock Payment']);
    }
}
