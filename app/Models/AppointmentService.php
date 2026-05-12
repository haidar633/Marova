<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class AppointmentService extends Model implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, CascadeSoftDeletes;
    use AuditableTrait;

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    protected $auditInclude = [
        'appointment_id',
        'doctor_service_id',
        'price',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function appointments()
    {
        return $this->hasMany(DutyScheduling::class, 'appointment_id');
    }

    public function doctor_services()
    {
        return $this->hasMany(DoctorService::class, 'doctor_service_id');
    }
}
