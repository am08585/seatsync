<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $reservation_id
 * @property int $user_id
 * @property string $action
 * @property array|null $details
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ReservationLog extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationLogFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reservation_id',
        'user_id',
        'action',
        'details',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reservation_id' => 'integer',
            'user_id' => 'integer',
            'action' => 'string',
            'details' => 'array',
        ];
    }

    /**
     * Get the reservation for the log.
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the user for the log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
