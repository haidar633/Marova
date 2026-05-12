<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class EMR extends Model implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use AuditableTrait;

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    protected $table = 'emrs';

    protected $auditInclude = [
        'patient_id',
        'title',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function emrDetails()
    {
        return $this->hasMany(EmrDetails::class, 'emr_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
