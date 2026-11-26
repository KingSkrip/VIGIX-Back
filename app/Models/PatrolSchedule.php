<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatrolSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'policy_id', 'company_id', 'scheduled_date', 'patrol_number', 'planned_start_at',
        'planned_end_at', 'generation_reference', 'status', 'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'planned_start_at' => 'datetime',
            'planned_end_at' => 'datetime',
            'generated_at' => 'datetime',
        ];
    }

    public function policy(): BelongsTo { return $this->belongsTo(PatrolPolicy::class, 'policy_id'); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function runs(): HasMany { return $this->hasMany(PatrolRun::class, 'schedule_id'); }
}