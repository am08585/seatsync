<?php

use App\Http\Controllers\PaymentMockController;
use App\Http\Controllers\SeatHoldController;
use App\Livewire\PaymentMockPage;
use App\Livewire\ReservationSummaryPage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::post('/screenings/{screening}/seats/{seat}/hold', [SeatHoldController::class, 'holdSeat'])
        ->name('seat-holds.hold');

    Route::delete('/screenings/{screening}/seats/{seat}/hold', [SeatHoldController::class, 'releaseSeat'])
        ->name('seat-holds.release');

    Route::livewire('/payment/mock/{hold_token}', PaymentMockPage::class)
        ->name('payment.mock.show');

    Route::post('/payment/mock/{hold_token}/success', [PaymentMockController::class, 'success'])
        ->name('payment.mock.success');

    Route::post('/payment/mock/{hold_token}/fail', [PaymentMockController::class, 'fail'])
        ->name('payment.mock.fail');

    Route::livewire('/reservations/{reservation}', ReservationSummaryPage::class)
        ->whereNumber('reservation')
        ->name('reservations.summary');
});
