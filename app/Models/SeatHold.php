<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $screening_id
 * @property int $seat_id
 * @property string $hold_token
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon $created_at
 */
class SeatHold extends Model
{
    /** @use HasFactory<\Database\Factories\SeatHoldFactory> */
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'screening_id',
        'seat_id',
        'hold_token',
        'expires_at',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user for the seat hold.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the screening for the seat hold.
     */
    public function screening()
    {
        return $this->belongsTo(Screening::class);
    }

    /**
     * Get the seat for the seat hold.
     */
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
