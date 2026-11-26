<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatrolDailySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'guard_id', 'report_date', 'scheduled_patrols', 'completed_patrols',
        'incomplete_patrols', 'missed_patrols', 'expected_points', 'completed_points',
        'incidents_count', 'late_scans', 'compliance_percentage', 'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'compliance_percentage' => 'decimal:2',
            'generated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function guard(): BelongsTo { return $this->belongsTo(User::class, 'guard_id'); }
}