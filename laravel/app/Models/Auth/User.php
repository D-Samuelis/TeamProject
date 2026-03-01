<?php

namespace App\Models\Auth;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Domain\Business\Entities\Branch;
use App\Domain\Business\Entities\Business;
use App\Domain\Business\Entities\Service;

use App\Domain\Business\Enums\BranchRoleEnum;
use App\Domain\Business\Enums\BusinessRoleEnum;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'country', 'city', 'title_prefix', 'birth_date', 'title_suffix', 'phone_number', 'gender', 'remember_token'];

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
        'birth_date' => 'date',
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
    public function hasBusinessRole($businessId, BusinessRoleEnum $role): bool
    {
        return $this->businesses()->where('business_id', $businessId)->wherePivot('role', $role->value)->exists();
    }

    public function hasBranchRole($branchId, BranchRoleEnum $role): bool
    {
        return $this->branches()->where('branch_id', $branchId)->wherePivot('role', $role->value)->exists();
    }
}