<?php

namespace App\Http\Controllers;

use App\Events\SeatHeld;
use App\Events\SeatReleased;
use App\Http\Requests\HoldSeatRequest;
use App\Http\Requests\ReleaseSeatRequest;
use App\Models\Screening;
use App\Models\Seat;
use App\Services\SeatHoldService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class SeatHoldController extends Controller
{
    public function __construct(public SeatHoldService $seatHoldService) {}

    public function holdSeat(HoldSeatRequest $request, Screening $screening, Seat $seat): JsonResponse
    {
        $user = $request->user();

        $holdToken = (string) ($request->session()->get($this->holdTokenSessionKey($screening)) ?? Str::random(40));

        $result = $this->seatHoldService->holdSeat(
            screeningId: $screening->getKey(),
            seatId: $seat->getKey(),
            userId: $user->getKey(),
            holdToken: $holdToken,
        );

        if ($result === false) {
            return response()->json(['message' => 'Seat is already held.'], 409);
        }

        event(new SeatHeld(
            screeningId: $screening->getKey(),
            seatId: $seat->getKey(),
            userId: $user->getKey(),
            expiresAt: $result['expires_at']->toIso8601String(),
        ));

        $request->session()->put($this->holdTokenSessionKey($screening), $result['hold_token']);

        return response()->json([
            'hold_token' => $result['hold_token'],
            'expires_at' => $result['expires_at']->toIso8601String(),
            'payment_url' => route('payment.mock.show', ['hold_token' => $result['hold_token']]),
        ], 201);
    }

    public function releaseSeat(ReleaseSeatRequest $request, Screening $screening, Seat $seat): JsonResponse
    {
        $user = $request->user();

        try {
            $released = $this->seatHoldService->releaseSeat(
                screeningId: $screening->getKey(),
                seatId: $seat->getKey(),
                userId: $user->getKey(),
            );
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'You are not allowed to release this seat.'], 403);
        }

        if (! $released) {
            return response()->json(['message' => 'Seat is not currently held.'], 404);
        }

        if (! \App\Models\SeatHold::query()->where('user_id', $user->getKey())->where('screening_id', $screening->getKey())->exists()) {
            $request->session()->forget($this->holdTokenSessionKey($screening));
        }

        event(new SeatReleased(
            screeningId: $screening->getKey(),
            seatId: $seat->getKey(),
            userId: $user->getKey(),
        ));

        return response()->json(['released' => true]);
    }

    private function holdTokenSessionKey(Screening $screening): string
    {
        return 'hold_token:screening:'.$screening->getKey();
    }
}
