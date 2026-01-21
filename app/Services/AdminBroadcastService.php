<?php

namespace App\Services;

use App\Events\ReservationCancelled;
use App\Events\ReservationCreated;
use App\Events\SeatReleased;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Broadcast;

/**
 * Service for handling admin dashboard broadcasting and real-time updates.
 */
class AdminBroadcastService
{
    /**
     * Broadcast dashboard updates when reservations are created.
     */
    public function broadcastReservationCreated(ReservationCreated $event): void
    {
        Broadcast::on(new PrivateChannel('admin-dashboard'))
            ->as('reservation-created')
            ->data([
                'reservation_id' => $event->reservation->id,
                'screening_id' => $event->reservation->screening_id,
                'total_price' => $event->reservation->total_price,
                'seat_count' => $event->reservation->seats()->count(),
            ])
            ->broadcast();
    }

    /**
     * Broadcast dashboard updates when reservations are cancelled.
     */
    public function broadcastReservationCancelled(ReservationCancelled $event): void
    {
        Broadcast::on(new PrivateChannel('admin-dashboard'))
            ->as('reservation-cancelled')
            ->data([
                'reservation_id' => $event->reservation->id,
                'screening_id' => $event->reservation->screening_id,
                'total_price' => $event->reservation->total_price,
                'cancelled_at' => $event->reservation->cancelled_at,
            ])
            ->broadcast();
    }

    /**
     * Broadcast dashboard updates when seats are released.
     */
    public function broadcastSeatReleased(SeatReleased $event): void
    {
        Broadcast::on(new PrivateChannel('admin-dashboard'))
            ->as('seat-released')
            ->data([
                'screening_id' => $event->screeningId,
                'seat_id' => $event->seatId,
                'user_id' => $event->userId,
            ])
            ->broadcast();
    }

    /**
     * Broadcast general dashboard statistics update.
     */
    public function broadcastDashboardUpdate(array $data = []): void
    {
        Broadcast::on(new PrivateChannel('admin-dashboard'))
            ->as('dashboard-update')
            ->data($data)
            ->broadcast();
    }

    /**
     * Broadcast abnormal activity alerts to admins.
     */
    public function broadcastAbnormalActivity(string $type, array $data = []): void
    {
        Broadcast::on(new PrivateChannel('admin-alerts'))
            ->as('abnormal-activity')
            ->data([
                'type' => $type,
                'data' => $data,
                'timestamp' => now(),
            ])
            ->broadcast();
    }
}