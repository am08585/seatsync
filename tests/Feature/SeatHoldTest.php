<?php

use App\Models\Screening;
use App\Models\Seat;
use App\Models\SeatHold;
use App\Models\Theater;
use App\Models\User;
use App\Services\SeatHoldService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;

uses(RefreshDatabase::class);

test('authenticated user can hold a seat', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create();
    $seat = Seat::factory()->for($theater)->create();

    $csrfToken = csrf_token();

    $key = "hold:screening:{$screening->getKey()}:seat:{$seat->getKey()}";

    Redis::shouldReceive('set')
        ->once()
        ->withArgs(function ($paramKey, $value, $option, $ttl, $flag) use ($key) {
            return $paramKey === $key
                && $option === 'EX'
                && $ttl === SeatHoldService::HOLD_TTL_SECONDS
                && $flag === 'NX';
        })
        ->andReturn('OK');

    $response = $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('seat-holds.hold', ['screening' => $screening, 'seat' => $seat]), ['_token' => $csrfToken]);

    $response
        ->assertStatus(201)
        ->assertJsonStructure([
            'hold_token',
            'expires_at',
            'payment_url',
        ]);

    $holdToken = $response->json('hold_token');

    $this->assertDatabaseHas('seat_holds', [
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
        'hold_token' => $holdToken,
    ]);

});

test('holding an already held seat returns conflict', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create();
    $seat = Seat::factory()->for($theater)->create();

    $csrfToken = csrf_token();

    $key = "hold:screening:{$screening->getKey()}:seat:{$seat->getKey()}";

    Redis::shouldReceive('set')
        ->twice()
        ->withArgs(function ($paramKey, $value, $option, $ttl, $flag) use ($key) {
            return $paramKey === $key
                && $option === 'EX'
                && $ttl === SeatHoldService::HOLD_TTL_SECONDS
                && $flag === 'NX';
        })
        ->andReturn('OK', null);

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('seat-holds.hold', ['screening' => $screening, 'seat' => $seat]), ['_token' => $csrfToken])
        ->assertStatus(201);

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('seat-holds.hold', ['screening' => $screening, 'seat' => $seat]), ['_token' => $csrfToken])
        ->assertStatus(409);

    expect(SeatHold::query()->count())->toBe(1);

});

test('user can release their held seat', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create();
    $seat = Seat::factory()->for($theater)->create();

    $csrfToken = csrf_token();

    SeatHold::query()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
        'hold_token' => 'token',
        'expires_at' => now()->addMinutes(10),
        'created_at' => now(),
    ]);

    $key = "hold:screening:{$screening->getKey()}:seat:{$seat->getKey()}";

    Redis::shouldReceive('command')
        ->once()
        ->with('GET', [$key])
        ->andReturn(json_encode([
            'user_id' => $user->getKey(),
            'hold_token' => 'token',
            'expires_at' => now()->addMinutes(10)->toIso8601String(),
        ]));

    Redis::shouldReceive('command')
        ->once()
        ->with('DEL', [$key])
        ->andReturn(1);

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->delete(route('seat-holds.release', ['screening' => $screening, 'seat' => $seat]), ['_token' => $csrfToken])
        ->assertStatus(200)
        ->assertJson(['released' => true]);

    $this->assertDatabaseMissing('seat_holds', [
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
    ]);
});

test('expired holds are cleaned up when redis key is missing', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create();
    $seat = Seat::factory()->for($theater)->create();

    $csrfToken = csrf_token();

    SeatHold::factory()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
    ]);

    $key = "hold:screening:{$screening->getKey()}:seat:{$seat->getKey()}";

    Redis::shouldReceive('command')
        ->once()
        ->with('GET', [$key])
        ->andReturn(null);

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->delete(route('seat-holds.release', ['screening' => $screening, 'seat' => $seat]), ['_token' => $csrfToken])
        ->assertStatus(404);

    $this->assertDatabaseMissing('seat_holds', [
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
    ]);
});
