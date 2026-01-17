<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class SeatReleased implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public int $screeningId,
        public int $seatId,
        public int $userId,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('screening.'.$this->screeningId)];
    }

    /**
     * @return array<string, int>
     */
    public function broadcastWith(): array
    {
        return [
            'screening_id' => $this->screeningId,
            'seat_id' => $this->seatId,
            'user_id' => $this->userId,
        ];
    }
}
