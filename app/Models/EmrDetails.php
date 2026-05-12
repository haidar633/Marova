<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class EmrDetails extends Model implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use AuditableTrait;

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    protected $auditInclude = [
        'emr_id',
        'description',
        'file',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function emr()
    {
        return $this->belongsTo(EMR::class, 'emr_id');
    }
}
