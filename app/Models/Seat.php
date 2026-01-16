<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $theater_id
 * @property string $row
 * @property int $number
 * @property int $price_modifier
 * @property string $seat_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Seat extends Model
{
    /** @use HasFactory<\Database\Factories\SeatFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'theater_id',
        'row',
        'number',
        'price_modifier',
        'seat_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_modifier' => 'integer',
            'seat_type' => 'string',
        ];
    }

    /**
     * Get the theater for the seat.
     */
    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }

    /**
     * Get the seat holds for the seat.
     */
    public function seatHolds()
    {
        return $this->hasMany(SeatHold::class);
    }

    /**
     * Get the reservations for the seat (through pivot).
     */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_seat')->withPivot('price');
    }
}
