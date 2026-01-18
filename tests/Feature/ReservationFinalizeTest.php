<?php

use App\Events\SeatReleased;
use App\Events\SeatReserved;
use App\Models\Reservation;
use App\Models\Screening;
use App\Models\Seat;
use App\Models\SeatHold;
use App\Models\Theater;
use App\Models\User;
use App\Services\SeatHoldService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;

uses(RefreshDatabase::class);

test('payment success creates a confirmed reservation and clears holds', function () {
    Event::fake([SeatReserved::class, SeatReleased::class]);

    $user = User::factory()->create();
    $theater = Theater::factory()->create();

    $screening = Screening::factory()->for($theater)->create([
        'base_price' => 1500,
    ]);

    $seatA = Seat::factory()->for($theater)->create([
        'row' => 'A',
        'number' => 1,
        'seat_type' => 'standard',
        'price_modifier' => 0,
    ]);

    $seatB = Seat::factory()->for($theater)->create([
        'row' => 'A',
        'number' => 2,
        'seat_type' => 'vip',
        'price_modifier' => 400,
    ]);

    $holdToken = 'hold_token_123';

    SeatHold::query()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seatA->getKey(),
        'hold_token' => $holdToken,
        'expires_at' => now()->addSeconds(SeatHoldService::HOLD_TTL_SECONDS),
        'created_at' => now(),
    ]);

    SeatHold::query()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seatB->getKey(),
        'hold_token' => $holdToken,
        'expires_at' => now()->addSeconds(SeatHoldService::HOLD_TTL_SECONDS),
        'created_at' => now(),
    ]);

    $keyA = "hold:screening:{$screening->getKey()}:seat:{$seatA->getKey()}";
    $keyB = "hold:screening:{$screening->getKey()}:seat:{$seatB->getKey()}";

    Redis::shouldReceive('command')
        ->once()
        ->with('GET', [$keyA])
        ->andReturn(json_encode([
            'user_id' => $user->getKey(),
            'hold_token' => $holdToken,
            'expires_at' => now()->addSeconds(SeatHoldService::HOLD_TTL_SECONDS)->toIso8601String(),
        ]));

    Redis::shouldReceive('command')
        ->once()
        ->with('GET', [$keyB])
        ->andReturn(json_encode([
            'user_id' => $user->getKey(),
            'hold_token' => $holdToken,
            'expires_at' => now()->addSeconds(SeatHoldService::HOLD_TTL_SECONDS)->toIso8601String(),
        ]));

    Redis::shouldReceive('command')
        ->once()
        ->with('DEL', [$keyA, $keyB])
        ->andReturn(2);

    $csrfToken = csrf_token();

    $response = $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('payment.mock.success', ['hold_token' => $holdToken]), ['_token' => $csrfToken]);

    $reservation = Reservation::query()->where('hold_token', $holdToken)->first();

    $response->assertStatus(302);

    expect($reservation)->not->toBeNull();

    $response->assertRedirect(route('reservations.summary', ['reservation' => $reservation]));

    $this->assertDatabaseHas('reservations', [
        'id' => $reservation->getKey(),
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'hold_token' => $holdToken,
        'status' => 'confirmed',
        'total_price' => 3400,
    ]);

    $this->assertDatabaseHas('reservation_seat', [
        'reservation_id' => $reservation->getKey(),
        'seat_id' => $seatA->getKey(),
        'price' => 1500,
    ]);

    $this->assertDatabaseHas('reservation_seat', [
        'reservation_id' => $reservation->getKey(),
        'seat_id' => $seatB->getKey(),
        'price' => 1900,
    ]);

    $this->assertDatabaseMissing('seat_holds', [
        'hold_token' => $holdToken,
    ]);

    Event::assertDispatchedTimes(SeatReserved::class, 2);
    Event::assertNotDispatched(SeatReleased::class);
});

test('payment failure releases holds and does not create a reservation', function () {
    Event::fake([SeatReserved::class, SeatReleased::class]);

    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create();

    $seat = Seat::factory()->for($theater)->create([
        'row' => 'B',
        'number' => 1,
        'seat_type' => 'standard',
        'price_modifier' => 0,
    ]);

    $holdToken = 'hold_token_fail';

    SeatHold::query()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
        'hold_token' => $holdToken,
        'expires_at' => now()->addSeconds(SeatHoldService::HOLD_TTL_SECONDS),
        'created_at' => now(),
    ]);

    $key = "hold:screening:{$screening->getKey()}:seat:{$seat->getKey()}";

    Redis::shouldReceive('command')
        ->once()
        ->with('GET', [$key])
        ->andReturn(json_encode([
            'user_id' => $user->getKey(),
            'hold_token' => $holdToken,
            'expires_at' => now()->addSeconds(SeatHoldService::HOLD_TTL_SECONDS)->toIso8601String(),
        ]));

    Redis::shouldReceive('command')
        ->once()
        ->with('DEL', [$key])
        ->andReturn(1);

    $csrfToken = csrf_token();

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('payment.mock.fail', ['hold_token' => $holdToken]), ['_token' => $csrfToken])
        ->assertStatus(302)
        ->assertRedirect('/');

    $this->assertDatabaseMissing('reservations', [
        'hold_token' => $holdToken,
    ]);

    $this->assertDatabaseMissing('seat_holds', [
        'hold_token' => $holdToken,
    ]);

    Event::assertDispatchedTimes(SeatReleased::class, 1);
    Event::assertNotDispatched(SeatReserved::class);
});

test('expired hold blocks reservation', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create();
    $seat = Seat::factory()->for($theater)->create();

    $holdToken = 'expired_hold';

    SeatHold::query()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
        'hold_token' => $holdToken,
        'expires_at' => now()->subMinute(),
        'created_at' => now()->subMinutes(11),
    ]);

    Redis::shouldReceive('command')->never();

    $csrfToken = csrf_token();

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('payment.mock.success', ['hold_token' => $holdToken]), ['_token' => $csrfToken])
        ->assertStatus(422);

    $this->assertDatabaseMissing('reservations', [
        'hold_token' => $holdToken,
    ]);

    $this->assertDatabaseHas('seat_holds', [
        'hold_token' => $holdToken,
    ]);
});

test('wrong user hold token is forbidden', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create();
    $seat = Seat::factory()->for($theater)->create();

    $holdToken = 'not_yours';

    SeatHold::query()->create([
        'user_id' => $owner->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
        'hold_token' => $holdToken,
        'expires_at' => now()->addMinutes(10),
        'created_at' => now(),
    ]);

    Redis::shouldReceive('command')->never();

    $csrfToken = csrf_token();

    $this
        ->actingAs($attacker)
        ->withSession(['_token' => $csrfToken])
        ->post(route('payment.mock.success', ['hold_token' => $holdToken]), ['_token' => $csrfToken])
        ->assertStatus(403);
});

test('double reservation attempt fails with conflict', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create();
    $seat = Seat::factory()->for($theater)->create([
        'seat_type' => 'standard',
        'price_modifier' => 0,
    ]);

    $holdToken = 'idempotent_token';

    SeatHold::query()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
        'hold_token' => $holdToken,
        'expires_at' => now()->addMinutes(10),
        'created_at' => now(),
    ]);

    $key = "hold:screening:{$screening->getKey()}:seat:{$seat->getKey()}";

    Redis::shouldReceive('command')
        ->once()
        ->with('GET', [$key])
        ->andReturn(json_encode([
            'user_id' => $user->getKey(),
            'hold_token' => $holdToken,
            'expires_at' => now()->addMinutes(10)->toIso8601String(),
        ]));

    Redis::shouldReceive('command')
        ->once()
        ->with('DEL', [$key])
        ->andReturn(1);

    $csrfToken = csrf_token();

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('payment.mock.success', ['hold_token' => $holdToken]), ['_token' => $csrfToken])
        ->assertStatus(302);

    Redis::shouldReceive('command')->never();

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('payment.mock.success', ['hold_token' => $holdToken]), ['_token' => $csrfToken])
        ->assertStatus(409);
});

test('a seat cannot be reserved twice', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create([
        'base_price' => 1500,
    ]);
    $seat = Seat::factory()->for($theater)->create([
        'seat_type' => 'standard',
        'price_modifier' => 0,
    ]);

    $existingReservation = Reservation::factory()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'status' => 'confirmed',
        'total_price' => 1500,
    ]);

    $existingReservation->seats()->attach([
        $seat->getKey() => ['price' => 1500],
    ]);

    $holdToken = 'seat_taken';

    SeatHold::query()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
        'hold_token' => $holdToken,
        'expires_at' => now()->addMinutes(10),
        'created_at' => now(),
    ]);

    $key = "hold:screening:{$screening->getKey()}:seat:{$seat->getKey()}";

    Redis::shouldReceive('command')
        ->once()
        ->with('GET', [$key])
        ->andReturn(json_encode([
            'user_id' => $user->getKey(),
            'hold_token' => $holdToken,
            'expires_at' => now()->addMinutes(10)->toIso8601String(),
        ]));

    $csrfToken = csrf_token();

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('payment.mock.success', ['hold_token' => $holdToken]), ['_token' => $csrfToken])
        ->assertStatus(422);
});

test('missing redis hold blocks reservation finalization', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $screening = Screening::factory()->for($theater)->create();
    $seat = Seat::factory()->for($theater)->create();

    $holdToken = 'missing_redis_hold';

    SeatHold::query()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'seat_id' => $seat->getKey(),
        'hold_token' => $holdToken,
        'expires_at' => now()->addMinutes(10),
        'created_at' => now(),
    ]);

    $key = "hold:screening:{$screening->getKey()}:seat:{$seat->getKey()}";

    Redis::shouldReceive('command')
        ->once()
        ->with('GET', [$key])
        ->andReturn(null);

    $csrfToken = csrf_token();

    $this
        ->actingAs($user)
        ->withSession(['_token' => $csrfToken])
        ->post(route('payment.mock.success', ['hold_token' => $holdToken]), ['_token' => $csrfToken])
        ->assertStatus(422);
});
