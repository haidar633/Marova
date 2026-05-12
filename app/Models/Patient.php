<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Patient extends Model implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, CascadeSoftDeletes;
    use AuditableTrait;
    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    protected $auditInclude = [
        'fname',
        'lname',
        'email',
        'date_of_birth',
        'address',
        'serial_nb',
        'medications',
        'allergies',
        'height',
        'weight',
        'chronic_disease',
        'referral',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function contactDetails()
    {
        return $this->hasMany(ContactDetails::class);
    }

    public function emr(): BelongsTo
    {
        return $this->belongsTo(EMR::class);
    }

    public function details()
    {
        return $this->hasOne(PatientDetails::class);
    }

}
