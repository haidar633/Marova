<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'star_rating' => 'decimal:1',
        'last_day_we_met' => 'date',
        'is_favorite' => 'boolean',
    ];

    /**
     * Get the time passed since last meeting
     */
    public function getTimePassedAttribute()
    {
        if (!$this->last_day_we_met) {
            return null;
        }

        $now = \Carbon\Carbon::now()->startOfDay();
        $lastMet = \Carbon\Carbon::parse($this->last_day_we_met)->startOfDay();
        
        // Only calculate if date is in the past
        if ($lastMet->isPast() || $lastMet->isToday()) {
            $diff = $now->diff($lastMet);
            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->d;
            
            $parts = [];
            if ($years > 0) {
                $parts[] = $years . ' year' . ($years > 1 ? 's' : '');
            }
            if ($months > 0) {
                $parts[] = $months . ' month' . ($months > 1 ? 's' : '');
            }
            if ($days > 0) {
                $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
            }
            
            if (empty($parts)) {
                return 'Today';
            } else {
                return implode(' ', $parts);
            }
        }
        
        return null;
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // Clean the path
        $path = ltrim($this->image, '/');

        // Check if file exists in storage
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        return null;
    }

    public function meetings()
    {
        return $this->hasMany(PartnerMeeting::class);
    }

    /**
     * Check if image exists
     */
    public function hasImage()
    {
        if (!$this->image) {
            return false;
        }

        $path = ltrim($this->image, '/');
        return Storage::disk('public')->exists($path);
    }
}
