<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property int|null $total_seats
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Theater extends Model
{
    /** @use HasFactory<\Database\Factories\TheaterFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'total_seats',
    ];

    /**
     * Get the seats for the theater.
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Get the screenings for the theater.
     */
    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}
