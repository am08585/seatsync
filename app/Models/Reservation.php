<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $screening_id
 * @property int $total_price
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'screening_id',
        'hold_token',
        'total_price',
        'status',
        'payment_reference',
        'confirmed_at',
        'cancelled_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_price' => 'integer',
            'status' => 'string',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Get the user for the reservation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the screening for the reservation.
     */
    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    /**
     * Get the seats for the reservation (through pivot).
     */
    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'reservation_seat')->withPivot('price');
    }

    /**
     * Get the reservation logs for the reservation.
     */
    public function logs()
    {
        return $this->hasMany(ReservationLog::class);
    }
}
