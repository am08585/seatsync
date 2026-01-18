<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class SeatReserved implements ShouldBroadcastNow, ShouldDispatchAfterCommit
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public int $screeningId,
        public int $seatId,
        public int $reservationId,
        public int $userId,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('screening.'.$this->screeningId)];
    }

    public function broadcastWith(): array
    {
        return [
            'screening_id' => $this->screeningId,
            'seat_id' => $this->seatId,
            'reservation_id' => $this->reservationId,
            'user_id' => $this->userId,
        ];
    }
}
