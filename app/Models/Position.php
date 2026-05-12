<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Position extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    // Scope for favorite positions
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }
    public function scopeRegular($query)
    {
        return $query->where('is_favorite', false);
    }


    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return null;
        }

        // Clean the path
        $path = ltrim($this->photo, '/');

        // Check if file exists in storage
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        return null;
    }

    /**
     * Check if photo exists
     */
    public function hasPhoto()
    {
        if (!$this->photo) {
            return false;
        }

        $path = ltrim($this->photo, '/');
        return Storage::disk('public')->exists($path);
    }
}
