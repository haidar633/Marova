<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoodJournal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'entry_date' => 'date',
        'discussed_with_partner' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vibeCheck(): BelongsTo
    {
        return $this->belongsTo(VibeCheck::class);
    }

    public function talkTrackers()
    {
        return $this->hasMany(TalkTracker::class);
    }
}
