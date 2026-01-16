<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = ['Action', 'Drama', 'Comedy', 'Horror', 'Sci-Fi', 'Romance'];

        foreach ($genres as $genreName) {
            Genre::firstOrCreate(['name' => $genreName]);
        }
    }
}
