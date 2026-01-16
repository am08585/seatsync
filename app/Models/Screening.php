<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $movie_id
 * @property int $theater_id
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon $end_time
 * @property int $base_price
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Screening extends Model
{
    /** @use HasFactory<\Database\Factories\ScreeningFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'movie_id',
        'theater_id',
        'start_time',
        'end_time',
        'base_price',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'base_price' => 'integer',
        ];
    }

    /**
     * Get the movie for the screening.
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Get the theater for the screening.
     */
    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }

    /**
     * Get the reservations for the screening.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the seat holds for the screening.
     */
    public function seatHolds()
    {
        return $this->hasMany(SeatHold::class);
    }
}
