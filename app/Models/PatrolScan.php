<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatrolScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_uuid', 'patrol_run_id', 'run_point_id', 'qr_point_id', 'guard_id', 'device_id',
        'client_scanned_at', 'received_at', 'ip_address', 'latitude', 'longitude',
        'source', 'validation_status', 'validation_reason', 'validated_at', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'client_scanned_at' => 'datetime:Y-m-d H:i:s.u',
            'received_at' => 'datetime:Y-m-d H:i:s.u',
            'validated_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'metadata' => 'array',
        ];
    }

    public function run(): BelongsTo { return $this->belongsTo(PatrolRun::class, 'patrol_run_id'); }
    public function runPoint(): BelongsTo { return $this->belongsTo(PatrolRunPoint::class, 'run_point_id'); }
    public function qrPoint(): BelongsTo { return $this->belongsTo(QrPoint::class); }
    public function guard(): BelongsTo { return $this->belongsTo(User::class, 'guard_id'); }
    public function device(): BelongsTo { return $this->belongsTo(Device::class); }
}