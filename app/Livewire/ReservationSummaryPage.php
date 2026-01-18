<?php

namespace App\Livewire;

use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ReservationSummaryPage extends Component
{
    #[Locked]
    public int $reservationId;

    public function mount(Reservation $reservation): void
    {
        $this->reservationId = $reservation->getKey();
    }

    public function render()
    {
        $user = Auth::user();

        if ($user === null) {
            abort(403);
        }

        $reservation = Reservation::query()
            ->with(['screening.movie', 'screening.theater', 'seats'])
            ->findOrFail($this->reservationId);

        if ((int) $reservation->user_id !== (int) $user->getKey()) {
            abort(403);
        }

        return view('livewire.reservation-summary-page', [
            'reservation' => $reservation,
        ])->layout('layouts.app', ['title' => 'Reservation Summary']);
    }
}
