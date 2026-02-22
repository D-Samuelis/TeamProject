<?php

namespace App\Models;

use App\Enums\BranchRole;
use App\Enums\BusinessRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relations
     */
    public function businesses()
    {
        return $this->belongsToMany(Business::class)->withPivot('role')->withTimestamps();
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class)->withPivot('role')->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Helpers
     *  */
    public function hasBusinessRole($businessId, BusinessRole $role): bool
    {
        return $this->businesses()->where('business_id', $businessId)->wherePivot('role', $role->value)->exists();
    }

    public function hasBranchRole($branchId, BranchRole $role): bool
    {
        return $this->branches()->where('branch_id', $branchId)->wherePivot('role', $role->value)->exists();
    }
}
