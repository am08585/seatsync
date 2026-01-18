<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can access the movies page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('movies.index'));

    $response->assertStatus(200);
});

test('unauthenticated users are redirected from movies page', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
