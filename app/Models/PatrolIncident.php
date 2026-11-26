<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatrolIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'patrol_run_id', 'guard_id', 'qr_point_id', 'incident_type', 'title', 'description',
        'priority', 'status', 'resolved_by', 'resolution_notes', 'reported_at', 'resolved_at',
    ];

    protected function casts(): array
    {
        return ['reported_at' => 'datetime', 'resolved_at' => 'datetime'];
    }

    public function run(): BelongsTo { return $this->belongsTo(PatrolRun::class, 'patrol_run_id'); }
    public function guard(): BelongsTo { return $this->belongsTo(User::class, 'guard_id'); }
    public function qrPoint(): BelongsTo { return $this->belongsTo(QrPoint::class); }
    public function resolver(): BelongsTo { return $this->belongsTo(User::class, 'resolved_by'); }
}