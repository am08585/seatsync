<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Event fired when a reservation is cancelled to notify users watching the seat map.
 */
class SeatCancellationNotice implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public int $screeningId,
        public array $seatIds,
        public string $message = 'Some seats have become available due to a cancellation.',
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('screening.'.$this->screeningId)];
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'screening_id' => $this->screeningId,
            'seat_ids' => $this->seatIds,
            'message' => $this->message,
        ];
    }
}
