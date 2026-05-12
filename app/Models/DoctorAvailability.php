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

class DoctorAvailability extends Model implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, CascadeSoftDeletes;
    use AuditableTrait;

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    protected $auditInclude = [
        'user_id',
        'day',
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
