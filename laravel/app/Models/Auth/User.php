<?php

namespace App\Models\Auth;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Models\Business\Appointment;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'country',
        'city',
        'title_prefix',
        'birth_date',
        'title_suffix',
        'phone_number',
        'gender',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'birth_date'        => 'date',
        'is_admin'          => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function businesses()
    {
        return $this->morphedByMany(Business::class, 'model', 'model_has_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function branches()
    {
        return $this->morphedByMany(Branch::class, 'model', 'model_has_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function branchServices()
    {
        return $this->morphedByMany(\App\Models\Business\BranchService::class, 'model', 'model_has_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function hasBusinessRole(int $businessId, string $role): bool
    {
        return $this->businesses()
            ->wherePivot('model_id', $businessId)
            ->wherePivot('role', $role)
            ->exists();
    }

    public function hasBranchRole(int $branchId, string $role): bool
    {
        return $this->branches()
            ->wherePivot('model_id', $branchId)
            ->wherePivot('role', $role)
            ->exists();
    }
}
