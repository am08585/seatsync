<?php

namespace App\Http\Controllers;

use App\Exceptions\ReservationAlreadyProcessedException;
use App\Services\ReservationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class PaymentMockController extends Controller
{
    public function __construct(public ReservationService $reservationService) {}

    public function success(string $hold_token): RedirectResponse
    {
        try {
            $reservation = $this->reservationService->finalize($hold_token, 'success');
        } catch (AuthorizationException $e) {
            abort(403);
        } catch (ReservationAlreadyProcessedException $e) {
            abort(409);
        } catch (ValidationException $e) {
            abort(422);
        }

        if ($reservation === null) {
            abort(422);
        }

        return redirect()->route('reservations.summary', ['reservation' => $reservation]);
    }

    public function fail(string $hold_token): RedirectResponse
    {
        try {
            $this->reservationService->finalize($hold_token, 'fail');
        } catch (AuthorizationException $e) {
            abort(403);
        } catch (ReservationAlreadyProcessedException $e) {
            abort(409);
        } catch (ValidationException $e) {
            abort(422);
        }

        return redirect('/');
    }
}
