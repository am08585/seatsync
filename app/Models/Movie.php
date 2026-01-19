<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Get the full URL for the poster image.
     */
    public function getPosterUrlAttribute(): string
    {
        return $this->poster_path ? asset('storage/'.$this->poster_path) : '';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::forceDeleted(function ($movie) {
            // Check if the movie has an associated file path
            if ($movie->poster_path) {
                // Delete the file using the Storage facade
                Storage::disk('public')->delete($movie->poster_path);
            }
        });
    }
}
