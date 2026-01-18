<?php

namespace App\Livewire;

use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class ReservationList extends Component
{
    public Collection $upcomingReservations;

    public Collection $pastReservations;

    public function mount(): void
    {
        $this->loadReservations();
    }

    public function loadReservations(): void
    {
        $user = auth()->user();

        if ($user === null) {
            $this->upcomingReservations = collect();
            $this->pastReservations = collect();

            return;
        }

        $reservations = Reservation::query()
            ->with(['screening.movie', 'seats'])
            ->where('user_id', $user->id)
            ->get()
            ->sortByDesc(function (Reservation $reservation) {
                return $reservation->screening->start_time;
            })
            ->groupBy(function (Reservation $reservation) {
                return $reservation->screening->start_time->isFuture() ? 'upcoming' : 'past';
            });

        $this->upcomingReservations = $reservations->get('upcoming', collect());
        $this->pastReservations = $reservations->get('past', collect());
    }

    public function confirmCancel(Reservation $reservation): void
    {
        $this->dispatch('open-cancel-modal', reservation: $reservation);
    }

    public function render(): View
    {
        return view('livewire.reservation-list')
            ->layout('layouts.app', ['title' => 'My Reservations']);
    }
}
