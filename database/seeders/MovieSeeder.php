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
                'title' => 'Dune: Part Two',
                'description' => 'Dune: Part Two explores the mythic journey of Paul Atreides as he unites with Chani and the Fremen while on a warpath of revenge against the conspirators who destroyed his family. Facing a choice between the love of his life and the fate of the known universe, he endeavors to prevent a terrible future only he can foresee.',
                'runtime' => 166,
                'poster_path' => 'movies/posters/01KFA56RF6VNX8PMKRBEAVC1AA.jpg',
                'genres' => ['Sci-Fi', 'Adventure'],
            ],
            [
                'title' => 'John Wick: Chapter 4',
                'description' => 'John Wick (Keanu Reeves) takes on his most lethal adversaries yet in the upcoming fourth installment of the series. With the price on his head ever increasing, Wick takes his fight against the High Table global as he seeks out the most powerful players in the underworld, from New York to Paris to Osaka to Berlin.',
                'runtime' => 169,
                'poster_path' => 'movies/posters/01KFDSBAKCPVDE942K3ZWXC1ZS.jpg',
                'genres' => ['Action', 'Thriller'],
            ],
            [
                'title' => '300: Rise of an Empire',
                'description' => 'Xerxes, seeking revenge for his father\'s death, declares war on Greece. As a result, Themistocles, the Athenian admiral, is compelled to form an alliance with Sparta to protect Athens.',
                'runtime' => 102,
                'poster_path' => 'movies/posters/01KFDT0E0ENGZEJ60Q2WF79TQ6.jpg',
                'genres' => ['Action', 'History'],
            ],
            [
                'title' => 'War for the Planet of the Apes',
                'description' => 'Caesar (Andy Serkis) and his apes are forced into a deadly conflict with an army of humans led by a ruthless colonel (Woody Harrelson). After the apes suffer unimaginable losses, Caesar wrestles with his darker instincts and begins his own mythic quest to avenge his kind. As the journey finally brings them face to face, Caesar and the colonel are pitted against each other in an epic battle that will determine the fate of both of their species and the future of the planet.',
                'runtime' => 140,
                'poster_path' => 'movies/posters/01KFDTCSG458276HVAXBWQTA53.png',
                'genres' => ['Action', 'Sci-Fi'],
            ],
            [
                'title' => 'Mr. & Mrs. Smith',
                'description' => 'John and Jane Smith, a couple in a stagnating marriage, live a deceptively mundane existence. However, each has been hiding a secret from the other: they are assassins working for adversarial agencies. When they are both assigned to kill the same target, Benjamin Danz, the truth comes to the surface. Finally free from their cover stories, they discover that they have been assigned to kill each other, sparking a series of explosive attacks.',
                'runtime' => 120,
                'poster_path' => 'movies/posters/01KFDTHNQW5WJQ3VR9HNTXGH4H.webp',
                'genres' => ['Action', 'Comedy'],
            ],
            [
                'title' => 'Avatar: Fire and Ash',
                'description' => 'The conflict on Pandora escalates as Jake and Neytiri\'s family encounter a new, aggressive Na\'vi tribe.',
                'runtime' => 197,
                'poster_path' => 'movies/posters/01KFA4TMKHNT7E21N27WF71177.jpg',
                'genres' => ['Action', 'Fantasy'],
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
