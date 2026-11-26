<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'company_id', 'employee_number', 'name', 'last_name', 'email', 'phone',
        'password', 'is_active', 'must_change_password', 'email_verified_at', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name.' '.$this->last_name);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    public function subroles(): MorphToMany
    {
        return $this->morphToMany(
            Subrole::class,
            'model',
            'model_has_subroles',
            'model_id',
            'subrole_id'
        );
    }

    public function permissions(): MorphToMany
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions', 'model_id', 'permission_id')
            ->withPivot('effect');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(DeviceCredential::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function guardAssignments(): HasMany
    {
        return $this->hasMany(GuardAssignment::class);
    }

    public function supervisorAssignments(): HasMany
    {
        return $this->hasMany(SupervisorAssignment::class, 'supervisor_id');
    }

    public function guardSupervisions(): HasMany
    {
        return $this->hasMany(SupervisorAssignment::class, 'guard_id');
    }

    public function patrolRuns(): HasMany
    {
        return $this->hasMany(PatrolRun::class, 'guard_id');
    }

    public function patrolScans(): HasMany
    {
        return $this->hasMany(PatrolScan::class, 'guard_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(PatrolIncident::class, 'guard_id');
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
