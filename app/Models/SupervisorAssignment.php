<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupervisorAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['supervisor_id', 'guard_id', 'company_id', 'shift_id', 'valid_from', 'valid_until', 'is_active'];

    protected function casts(): array
    {
        return ['valid_from' => 'date', 'valid_until' => 'date', 'is_active' => 'boolean'];
    }

    public function supervisor(): BelongsTo { return $this->belongsTo(User::class, 'supervisor_id'); }
    public function guard(): BelongsTo { return $this->belongsTo(User::class, 'guard_id'); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function shift(): BelongsTo { return $this->belongsTo(Shift::class); }
}