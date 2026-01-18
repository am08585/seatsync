<?php

use App\Models\Screening;
use App\Models\Seat;
use App\Services\ReservationService;

test('reservation service calculates seat price and total price', function () {
    $service = new ReservationService;

    $screening = new Screening([
        'base_price' => 1500,
    ]);

    $seatA = new Seat([
        'price_modifier' => 0,
    ]);

    $seatB = new Seat([
        'price_modifier' => 400,
    ]);

    expect($service->seatPriceCents($screening, $seatA))->toBe(1500);
    expect($service->seatPriceCents($screening, $seatB))->toBe(1900);
    expect($service->totalPriceCents($screening, [$seatA, $seatB]))->toBe(3400);
});
