<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrPoint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wing_id', 'name', 'code', 'description', 'token_hash', 'sequence_number',
        'latitude', 'longitude', 'geofence_radius', 'is_active',
    ];

    protected $hidden = ['token_hash'];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'sequence_number' => 'integer',
            'geofence_radius' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function wing(): BelongsTo { return $this->belongsTo(Wing::class); }
    public function history(): HasMany { return $this->hasMany(QrPointHistory::class); }
    public function runPoints(): HasMany { return $this->hasMany(PatrolRunPoint::class); }
}