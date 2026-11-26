<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'code', 'start_time', 'end_time', 'crosses_midnight', 'is_active'];

    protected function casts(): array
    {
        return ['crosses_midnight' => 'boolean', 'is_active' => 'boolean'];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function guardAssignments(): HasMany { return $this->hasMany(GuardAssignment::class); }
    public function patrolPolicies(): HasMany { return $this->hasMany(PatrolPolicy::class); }
}