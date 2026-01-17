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

class SeatHoldController extends Controller
{
    public function __construct(public SeatHoldService $seatHoldService) {}

    public function holdSeat(HoldSeatRequest $request, Screening $screening, Seat $seat): JsonResponse
    {
        $user = $request->user();

        $result = $this->seatHoldService->holdSeat(
            screeningId: $screening->getKey(),
            seatId: $seat->getKey(),
            userId: $user->getKey(),
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

        return response()->json([
            'hold_token' => $result['hold_token'],
            'expires_at' => $result['expires_at']->toIso8601String(),
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

        event(new SeatReleased(
            screeningId: $screening->getKey(),
            seatId: $seat->getKey(),
            userId: $user->getKey(),
        ));

        return response()->json(['released' => true]);
    }
}
