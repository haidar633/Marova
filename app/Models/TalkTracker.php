<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalkTracker extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'conversation_date' => 'date',
        'connection_rating' => 'decimal:1',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moodJournal(): BelongsTo
    {
        return $this->belongsTo(MoodJournal::class);
    }
}
