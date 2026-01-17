<?php

use App\Http\Controllers\SeatHoldController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::post('/screenings/{screening}/seats/{seat}/hold', [SeatHoldController::class, 'holdSeat'])
        ->name('seat-holds.hold');

    Route::delete('/screenings/{screening}/seats/{seat}/hold', [SeatHoldController::class, 'releaseSeat'])
        ->name('seat-holds.release');
});
