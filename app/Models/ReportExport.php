<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by', 'report_type', 'format', 'period_start', 'period_end',
        'file_path', 'status', 'error_message', 'generated_at',
    ];

    protected function casts(): array
    {
        return ['period_start' => 'date', 'period_end' => 'date', 'generated_at' => 'datetime'];
    }

    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function deliveries(): HasMany { return $this->hasMany(ReportDeliveryLog::class); }
}