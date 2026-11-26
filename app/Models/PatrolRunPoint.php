<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatrolRunPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'patrol_run_id', 'qr_point_id', 'point_name', 'point_code', 'wing_name',
        'sequence_number', 'planned_at', 'scanned_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'sequence_number' => 'integer',
            'planned_at' => 'datetime',
            'scanned_at' => 'datetime',
        ];
    }

    public function run(): BelongsTo { return $this->belongsTo(PatrolRun::class, 'patrol_run_id'); }
    public function qrPoint(): BelongsTo { return $this->belongsTo(QrPoint::class); }
    public function scans(): HasMany { return $this->hasMany(PatrolScan::class, 'run_point_id'); }
}