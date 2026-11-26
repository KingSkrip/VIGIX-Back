<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrPointHistory extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $table = 'qr_point_history';

    protected $fillable = ['qr_point_id', 'changed_by', 'action', 'old_values', 'new_values', 'reason'];

    protected function casts(): array
    {
        return ['old_values' => 'array', 'new_values' => 'array'];
    }

    public function qrPoint(): BelongsTo { return $this->belongsTo(QrPoint::class); }
    public function changedBy(): BelongsTo { return $this->belongsTo(User::class, 'changed_by'); }
}