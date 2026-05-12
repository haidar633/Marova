<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class NonWorkingDays extends Model implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, CascadeSoftDeletes;
    use AuditableTrait;

    protected $guarded = ['id'];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'range_start' => 'date',
        'range_end' => 'date',
        'excluded_dates' => 'array',
        'is_active' => 'boolean',
    ];

    protected $auditInclude = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'range_start',
        'range_end',
        'excluded_dates',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate])
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('type', '!=', 'single')
                        ->where(function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('range_start', [$startDate, $endDate])
                                ->orWhereBetween('range_end', [$startDate, $endDate]);
                        });
                });
        });
    }

    public function isNonWorkingDay($date)
    {
        $date = Carbon::parse($date);

        switch ($this->type) {
            case 'single':
                return $this->date->equalTo($date);

            case 'range':
            case 'whole_month':
                return $date->between($this->range_start, $this->range_end);

            case 'month_except':
                if (!$date->between($this->range_start, $this->range_end)) {
                    return false;
                }
                return !in_array($date->format('Y-m-d'), $this->excluded_dates ?? []);

            case 'random_days':
                return in_array($date->format('Y-m-d'), $this->excluded_dates ?? []);

            default:
                return false;
        }
    }

    public function getAvailableHours()
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        return [
            'start' => $this->start_time->format('H:i'),
            'end' => $this->end_time->format('H:i'),
        ];
    }
}
