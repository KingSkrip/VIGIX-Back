<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatrolException extends Model
{
    use HasFactory;

    protected $fillable = [
        'patrol_run_id', 'run_point_id', 'requested_by', 'approved_by',
        'exception_type', 'reason', 'status', 'requested_at', 'decided_at',
    ];

    protected function casts(): array
    {
        return ['requested_at' => 'datetime', 'decided_at' => 'datetime'];
    }

    public function run(): BelongsTo { return $this->belongsTo(PatrolRun::class, 'patrol_run_id'); }
    public function runPoint(): BelongsTo { return $this->belongsTo(PatrolRunPoint::class, 'run_point_id'); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
}