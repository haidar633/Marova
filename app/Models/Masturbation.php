<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Masturbation extends Model
{
    protected $guarded=['id'];

    protected $casts = [
        'vaseline_used' => 'boolean',
        'entry_date' => 'date',
    ];
//    public function user() {
//        return $this->belongsTo(User::class);
//    }
}
