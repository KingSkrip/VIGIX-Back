<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatrolPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'shift_id', 'name', 'description', 'patrols_per_day', 'coverage_type',
        'randomize_order', 'randomize_schedule', 'window_start', 'window_end',
        'start_tolerance_minutes', 'end_tolerance_minutes', 'minimum_interval_minutes',
        'active_days', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'randomize_order' => 'boolean',
            'randomize_schedule' => 'boolean',
            'active_days' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function shift(): BelongsTo { return $this->belongsTo(Shift::class); }
    public function schedules(): HasMany { return $this->hasMany(PatrolSchedule::class, 'policy_id'); }
}