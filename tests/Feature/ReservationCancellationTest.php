<?php

use App\Events\SeatCancellationNotice;
use App\Events\SeatReleased;
use App\Models\Movie;
use App\Models\Reservation;
use App\Models\ReservationLog;
use App\Models\Screening;
use App\Models\Seat;
use App\Models\Theater;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('user can view their own reservations', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $theater = Theater::factory()->create();
    $movie = Movie::factory()->create();

    $screening = Screening::factory()->for($theater)->for($movie)->create([
        'start_time' => now()->addDays(1),
    ]);

    $seat = Seat::factory()->for($theater)->create();

    // User's reservation
    $reservation = Reservation::factory()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'status' => 'confirmed',
        'total_price' => 1500,
    ]);

    $reservation->seats()->attach([$seat->getKey() => ['price' => 1500]]);

    // Other user's reservation
    $otherReservation = Reservation::factory()->create([
        'user_id' => $otherUser->getKey(),
        'screening_id' => $screening->getKey(),
        'status' => 'confirmed',
        'total_price' => 1500,
    ]);

    $otherReservation->seats()->attach([$seat->getKey() => ['price' => 1500]]);

    $response = $this->actingAs($user)->get(route('reservations.index'));

    $response->assertStatus(200);
    $response->assertSee($movie->title);
    $response->assertSee('CONFIRMED');
});

test('user cannot view others reservations', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $theater = Theater::factory()->create();
    $movie = Movie::factory()->create();

    $screening = Screening::factory()->for($theater)->for($movie)->create();

    $seat = Seat::factory()->for($theater)->create();

    // Other user's reservation
    $otherReservation = Reservation::factory()->create([
        'user_id' => $otherUser->getKey(),
        'screening_id' => $screening->getKey(),
        'status' => 'confirmed',
        'total_price' => 1500,
    ]);

    $response = $this->actingAs($user)->get(route('reservations.index'));

    $response->assertStatus(200);
    $response->assertDontSee($movie->title);
    $response->assertDontSee('Reservation #'.$otherReservation->id);
});

test('eligible reservation cancellation succeeds', function () {
    Event::fake([SeatReleased::class, SeatCancellationNotice::class]);

    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $movie = Movie::factory()->create();

    $screening = Screening::factory()->for($theater)->for($movie)->create([
        'start_time' => now()->addHours(2), // More than 60 minutes from now
    ]);

    $seatA = Seat::factory()->for($theater)->create(['row' => 'A', 'number' => 1]);
    $seatB = Seat::factory()->for($theater)->create(['row' => 'A', 'number' => 2]);

    $reservation = Reservation::factory()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'status' => 'confirmed',
        'total_price' => 3000,
    ]);

    $reservation->seats()->attach([
        $seatA->getKey() => ['price' => 1500],
        $seatB->getKey() => ['price' => 1500],
    ]);

    $this->actingAs($user);

    // Open the reservations page and click cancel
    $response = $this->get(route('reservations.index'));
    $response->assertStatus(200);
    $response->assertSee('Cancel Reservation');

    // Simulate the cancellation through Livewire
    \Livewire\Livewire::test(\App\Livewire\ReservationList::class)
        ->call('confirmCancel', $reservation->id);

    // Check that the modal opens
    \Livewire\Livewire::test(\App\Livewire\ReservationCancelConfirm::class)
        ->call('openModal', $reservation->id);

    // Actually cancel the reservation
    \Livewire\Livewire::test(\App\Livewire\ReservationCancelConfirm::class)
        ->call('openModal', $reservation->id)
        ->call('cancelReservation');

    // Check database state
    $this->assertDatabaseHas('reservations', [
        'id' => $reservation->getKey(),
        'status' => 'cancelled',
        'cancelled_at' => now(),
    ]);

    // Check seats are detached
    $this->assertDatabaseMissing('reservation_seat', [
        'reservation_id' => $reservation->getKey(),
    ]);

    // Check cancellation log
    $this->assertDatabaseHas('reservation_logs', [
        'reservation_id' => $reservation->getKey(),
        'user_id' => $user->getKey(),
        'action' => 'cancelled',
    ]);

    // Check events fired
    Event::assertDispatchedTimes(SeatReleased::class, 2);
    Event::assertDispatched(SeatCancellationNotice::class, function ($event) use ($screening, $seatA, $seatB) {
        return $event->screeningId === $screening->getKey()
            && in_array($seatA->getKey(), $event->seatIds)
            && in_array($seatB->getKey(), $event->seatIds);
    });
});

test('cancellation within 60 minutes fails', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $movie = Movie::factory()->create();

    $screening = Screening::factory()->for($theater)->for($movie)->create([
        'start_time' => now()->addMinutes(30), // Less than 60 minutes from now
    ]);

    $seat = Seat::factory()->for($theater)->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'status' => 'confirmed',
        'total_price' => 1500,
    ]);

    $reservation->seats()->attach([$seat->getKey() => ['price' => 1500]]);

    $this->actingAs($user);

    \Livewire\Livewire::test(\App\Livewire\ReservationCancelConfirm::class)
        ->call('openModal', $reservation->id)
        ->call('cancelReservation')
        ->assertHasErrors('validation');

    // Reservation should still be confirmed
    $this->assertDatabaseHas('reservations', [
        'id' => $reservation->getKey(),
        'status' => 'confirmed',
    ]);
});

test('cancellation updates reservation status and releases seats', function () {
    Event::fake([SeatReleased::class, SeatCancellationNotice::class]);

    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $movie = Movie::factory()->create();

    $screening = Screening::factory()->for($theater)->for($movie)->create([
        'start_time' => now()->addHours(2),
    ]);

    $seat = Seat::factory()->for($theater)->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'status' => 'confirmed',
        'total_price' => 1500,
    ]);

    $reservation->seats()->attach([$seat->getKey() => ['price' => 1500]]);

    $this->actingAs($user);

    \Livewire\Livewire::test(\App\Livewire\ReservationCancelConfirm::class)
        ->call('openModal', $reservation->id)
        ->call('cancelReservation');

    // Verify reservation status changed
    $updatedReservation = Reservation::find($reservation->id);
    expect($updatedReservation->status)->toBe('cancelled');
    expect($updatedReservation->cancelled_at)->not->toBeNull();

    // Verify seats released
    expect($updatedReservation->seats)->toBeEmpty();

    // Verify events fired
    Event::assertDispatched(SeatReleased::class);
    Event::assertDispatched(SeatCancellationNotice::class);
});

test('seat released and seat cancellation notice events are fired', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $movie = Movie::factory()->create();

    $screening = Screening::factory()->for($theater)->for($movie)->create([
        'start_time' => now()->addHours(2),
    ]);

    $seat = Seat::factory()->for($theater)->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'status' => 'confirmed',
        'total_price' => 1500,
    ]);

    $reservation->seats()->attach([$seat->getKey() => ['price' => 1500]]);

    Event::fake([SeatReleased::class, SeatCancellationNotice::class]);

    $this->actingAs($user);

    \Livewire\Livewire::test(\App\Livewire\ReservationCancelConfirm::class)
        ->call('openModal', $reservation->id)
        ->call('cancelReservation');

    Event::assertDispatched(SeatReleased::class, function ($event) use ($screening, $seat, $user) {
        return $event->screeningId === $screening->getKey()
            && $event->seatId === $seat->getKey()
            && $event->userId === $user->getKey();
    });

    Event::assertDispatched(SeatCancellationNotice::class, function ($event) use ($screening, $seat) {
        return $event->screeningId === $screening->getKey()
            && in_array($seat->getKey(), $event->seatIds)
            && $event->message === 'Some seats have become available due to a cancellation.';
    });
});

test('cancelled reservation page shows correct information', function () {
    $user = User::factory()->create();
    $theater = Theater::factory()->create();
    $movie = Movie::factory()->create();

    $screening = Screening::factory()->for($theater)->for($movie)->create([
        'start_time' => now()->addHours(2),
    ]);

    $seat = Seat::factory()->for($theater)->create();

    $reservation = Reservation::factory()->create([
        'user_id' => $user->getKey(),
        'screening_id' => $screening->getKey(),
        'status' => 'cancelled',
        'total_price' => 1500,
        'cancelled_at' => now(),
    ]);

    $reservation->seats()->attach([$seat->getKey() => ['price' => 1500]]);

    // Create cancellation log
    ReservationLog::create([
        'reservation_id' => $reservation->getKey(),
        'user_id' => $user->getKey(),
        'action' => 'cancelled',
        'details' => [
            'screening_id' => $screening->getKey(),
            'seat_ids' => [$seat->getKey()],
            'total_price' => 1500,
        ],
    ]);

    $this->actingAs($user);

    $response = $this->get(route('reservation.cancelled', ['reservation' => $reservation->id]));

    $response->assertStatus(200);
    $response->assertSee('Reservation Cancelled');
    $response->assertSee($movie->title);
    $response->assertSee($reservation->id);
    $response->assertSee('$15.00'); // Formatted total price
});
