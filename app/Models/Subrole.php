<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subrole extends Model
{
    use HasFactory;

    protected $fillable = ['role_id', 'name', 'slug', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function role(): BelongsTo { return $this->belongsTo(Role::class); }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'subrole_has_permissions', 'subrole_id', 'permission_id');
    }
}