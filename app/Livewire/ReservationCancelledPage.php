<?php

namespace App\Livewire;

use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Component;

class ReservationCancelledPage extends Component
{
    public Reservation $reservation;

    public array $releasedSeats = [];

    public string $cancelledAt = '';

    public function mount(Request $request): void
    {
        $reservationId = $request->query('reservation');

        if ($reservationId === null) {
            abort(404, 'Reservation not found');
        }

        $this->reservation = Reservation::query()
            ->with(['screening.movie', 'seats'])
            ->where('id', $reservationId)
            ->where('user_id', auth()->id())
            ->where('status', 'cancelled')
            ->firstOrFail();

        $this->cancelledAt = $this->reservation->cancelled_at?->format('F j, Y \a\t g:i A') ?? 'Unknown';

        // Get released seats from the cancellation log
        $cancellationLog = $this->reservation->logs()
            ->where('action', 'cancelled')
            ->latest()
            ->first();

        if ($cancellationLog !== null && isset($cancellationLog->details['seat_ids'])) {
            $this->releasedSeats = $cancellationLog->details['seat_ids'];
        }
    }

    public function render(): View
    {
        return view('livewire.reservation-cancelled-page')
            ->layout('layouts.app', ['title' => 'Reservation Cancelled']);
    }
}
