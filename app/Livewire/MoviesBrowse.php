<?php

namespace App\Livewire;

use App\Models\Movie;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class MoviesBrowse extends Component
{
    public Collection $movies;

    public string $search = '';

    public function mount(): void
    {
        $this->loadMovies();
    }

    public function loadMovies(): void
    {
        $query = Movie::query()
            ->with(['genres', 'screenings' => function ($query) {
                $query->where('start_time', '>', now())
                    ->orderBy('start_time')
                    ->with('theater');
            }]);

        if ($this->search !== '') {
            $query->whereRaw('LOWER(title) LIKE LOWER(?)', ['%'.$this->search.'%']);
        }

        $this->movies = $query->orderBy('title')->get();
    }

    public function updatedSearch(): void
    {
        $this->loadMovies();
    }

    public function render(): View
    {
        return view('livewire.movies-browse');
    }
}
