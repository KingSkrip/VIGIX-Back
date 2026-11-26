<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatrolRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id', 'guard_id', 'shift_id', 'started_at', 'finished_at',
        'total_points', 'completed_points', 'status', 'completion_notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'total_points' => 'integer',
            'completed_points' => 'integer',
        ];
    }

    public function schedule(): BelongsTo { return $this->belongsTo(PatrolSchedule::class, 'schedule_id'); }
    public function guard(): BelongsTo { return $this->belongsTo(User::class, 'guard_id'); }
    public function shift(): BelongsTo { return $this->belongsTo(Shift::class); }
    public function points(): HasMany { return $this->hasMany(PatrolRunPoint::class)->orderBy('sequence_number'); }
    public function scans(): HasMany { return $this->hasMany(PatrolScan::class); }
    public function incidents(): HasMany { return $this->hasMany(PatrolIncident::class); }
    public function exceptions(): HasMany { return $this->hasMany(PatrolException::class); }
}