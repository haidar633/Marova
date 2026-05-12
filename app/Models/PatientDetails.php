<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatientDetails extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
