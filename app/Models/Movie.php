<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $poster_path
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Movie extends Model
{
    /** @use HasFactory<\Database\Factories\MovieFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'runtime',
        'poster_path',
    ];

    /**
     * Get the genres for the movie.
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * Get the screenings for the movie.
     */
    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}
