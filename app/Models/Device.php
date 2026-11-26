<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'uuid', 'name', 'brand', 'model', 'device_type', 'operating_system', 'os_version',
        'browser', 'browser_version', 'app_version', 'platform', 'last_ip', 'registration_ip',
        'last_seen_at', 'approved_at', 'revoked_at', 'approved_by', 'status', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
            'approved_at' => 'datetime',
            'revoked_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function credentials(): HasMany { return $this->hasMany(DeviceCredential::class); }
    public function sessions(): HasMany { return $this->hasMany(UserSession::class); }
}