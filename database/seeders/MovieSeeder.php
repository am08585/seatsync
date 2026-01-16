<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            [
                'title' => 'The Last Horizon',
                'description' => 'A thrilling adventure across the final frontier of space.',
                'runtime' => 120,
                'poster_path' => '/images/the-last-horizon.jpg',
                'genres' => ['Sci-Fi', 'Action'],
            ],
            [
                'title' => 'Love in Winter',
                'description' => 'A heartwarming romance story set during the holiday season.',
                'runtime' => 95,
                'poster_path' => '/images/love-in-winter.jpg',
                'genres' => ['Romance', 'Drama'],
            ],
            [
                'title' => 'The Midnight Mystery',
                'description' => 'A suspenseful thriller that will keep you on the edge of your seat.',
                'runtime' => 105,
                'poster_path' => '/images/midnight-mystery.jpg',
                'genres' => ['Horror', 'Action'],
            ],
            [
                'title' => 'Laughter & Life',
                'description' => 'A hilarious comedy about family and friendship.',
                'runtime' => 110,
                'poster_path' => '/images/laughter-life.jpg',
                'genres' => ['Comedy', 'Drama'],
            ],
            [
                'title' => 'The Scientist',
                'description' => 'A drama about a brilliant scientist making a groundbreaking discovery.',
                'runtime' => 120,
                'poster_path' => '/images/the-scientist.jpg',
                'genres' => ['Drama', 'Sci-Fi'],
            ],
        ];

        foreach ($movies as $movieData) {
            $genres = $movieData['genres'];
            unset($movieData['genres']);

            $movie = Movie::firstOrCreate(
                ['title' => $movieData['title']],
                $movieData
            );

            foreach ($genres as $genreName) {
                $genre = Genre::where('name', $genreName)->first();
                if ($genre) {
                    $movie->genres()->syncWithoutDetaching($genre->id);
                }
            }
        }
    }
}
