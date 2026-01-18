<?php

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Screening;
use App\Models\Theater;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can browse movies', function () {
    $user = User::factory()->create();
    $movie = Movie::factory()->create(['title' => 'Test Movie']);
    $genre = Genre::factory()->create(['name' => 'Action']);
    $movie->genres()->attach($genre);

    $response = $this->actingAs($user)->get(route('movies.index'));

    $response->assertStatus(200);
    $response->assertSee('Test Movie');
    $response->assertSee('Action');
});

test('movies page shows upcoming screenings', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $movie = Movie::factory()->create(['title' => 'Test Movie']);

    $screening = Screening::factory()->create([
        'movie_id' => $movie->id,
        'theater_id' => $theater->id,
        'start_time' => now()->addHours(2),
        'base_price' => 1500,
    ]);

    $response = $this->actingAs($user)->get(route('movies.index'));

    $response->assertStatus(200);
    $response->assertSee('Test Movie');
    $response->assertSee('1 showings');
});

test('movie search functionality works', function () {
    $user = User::factory()->create();

    $movie1 = Movie::factory()->create(['title' => 'Action Movie']);
    $movie2 = Movie::factory()->create(['title' => 'Comedy Movie']);

    \Livewire\Livewire::actingAs($user)
        ->test(\App\Livewire\MoviesBrowse::class)
        ->set('search', 'Action')
        ->assertSee('Action Movie')
        ->assertDontSee('Comedy Movie');
});

test('movie screenings page displays correctly', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $movie = Movie::factory()->create(['title' => 'Test Movie']);

    $screening = Screening::factory()->create([
        'movie_id' => $movie->id,
        'theater_id' => $theater->id,
        'start_time' => now()->addHours(2),
        'base_price' => 1500,
    ]);

    $response = $this->actingAs($user)->get(route('screenings.show', ['movie' => $movie->id]));

    $response->assertStatus(200);
    $response->assertSee('Test Movie');
    $response->assertSee($theater->name);
});

test('empty movies page shows appropriate message', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('movies.index'));

    $response->assertStatus(200);
    $response->assertSee('No movies available');
});