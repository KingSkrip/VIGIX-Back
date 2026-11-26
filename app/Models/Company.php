<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'tax_identifier', 'address', 'timezone', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function users(): HasMany { return $this->hasMany(User::class); }
    public function wings(): HasMany { return $this->hasMany(Wing::class); }
    public function shifts(): HasMany { return $this->hasMany(Shift::class); }
    public function patrolPolicies(): HasMany { return $this->hasMany(PatrolPolicy::class); }
    public function patrolSchedules(): HasMany { return $this->hasMany(PatrolSchedule::class); }
}