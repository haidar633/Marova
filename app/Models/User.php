<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Spatie\Permission\Traits\HasRoles;
use Dyrynda\Database\Support\CascadeSoftDeletes;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, CascadeSoftDeletes;
    use AuditableTrait;

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $auditEvents = [
        'created',
        'updated',
        'deleted',
        'restored',
    ];

    protected $auditInclude = [
        'fname',
        'lname',
        'email',
        'date_of_birth',
        'address',
        'type',
        'about',
        'email_verified_at',
        'phone',
        'serial_nb',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $auditExclude = [
        'password',
        'remember_token',
    ];

    public function availability()
    {
        return $this->hasMany(DoctorAvailability::class, 'user_id');
    }
    protected $cascadeDeletes = ['comments'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userDetail()
    {
        return $this->hasOne(UserDetails::class);
    }

    public function services()
    {
        return $this->hasMany(DoctorService::class);
    }

    public function contactDetail()
    {
        return $this->hasMany(ContactDetails::class);
    }

    public function preferences()
    {
        return $this->hasOne(Preferences::class);
    }

    public function nonWorkingDays() {
        return $this->hasMany(NonWorkingDays::class, 'user_id');
    }

    // Boot method
    protected static function booted()
    {
        static::created(function ($user) {
            // Check if preferences exist, and create default ones if not
            if (!$user->preferences) {
                $user->preferences()->create([
                    'sidebar_color' => 'primary', // Default value
                    'dark_mode' => false, // Default value
                ]);
            }
        });
    }

    // Add this new scope method
    public function scopeSearchMany($query, array $fields, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        return $query->where(function ($query) use ($fields, $searchTerm) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', "%{$searchTerm}%");
            }
        });
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
}
