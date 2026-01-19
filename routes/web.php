<?php

use App\Http\Controllers\PaymentMockController;
use App\Http\Controllers\SeatHoldController;
use App\Livewire\MoviesBrowse;
use App\Livewire\MovieScreenings;
use App\Livewire\PaymentMockPage;
use App\Livewire\ReservationCancelledPage;
use App\Livewire\ReservationList;
use App\Livewire\ReservationSummaryPage;
use App\Livewire\SeatSelection;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::livewire('/', MoviesBrowse::class)->name('movies.index');
    Route::livewire('/movies/{movie}', MovieScreenings::class)
        ->whereNumber('movie')
        ->name('screenings.show');
    Route::livewire('/screenings/{screening}/seats', SeatSelection::class)
        ->whereNumber('screening')
        ->name('seat-selection.show');
});

Route::middleware(['web', 'auth'])->group(function () {
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

    Route::livewire('/reservations', ReservationList::class)
        ->name('reservations.index');

    Route::livewire('/reservations/{reservation}', ReservationSummaryPage::class)
        ->whereNumber('reservation')
        ->name('reservations.summary');

    Route::livewire('/reservations/cancelled', ReservationCancelledPage::class)
        ->name('reservation.cancelled');
});

Route::livewire('/practice', 'pages::practice')->name('practice');

Route::get('/uiux', function () {
    return view('uiux');
})->name('uiux');
