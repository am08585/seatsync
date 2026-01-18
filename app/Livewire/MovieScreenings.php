<?php

namespace App\Livewire;

use App\Models\Movie;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MovieScreenings extends Component
{
    public Movie $movie;

    public function mount(Movie $movie): void
    {
        $this->movie = $movie->load(['genres']);
    }

    public function render(): View
    {
        $screenings = $this->movie->screenings()
            ->with(['theater', 'reservations' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($screening) {
                return $screening->start_time->format('Y-m-d');
            });

        return view('livewire.movie-screenings', [
            'screenings' => $screenings,
        ])->layout('layouts.app', ['title' => $this->movie->title.' - Showtimes']);
    }
}
