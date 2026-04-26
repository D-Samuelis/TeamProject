<?php

namespace App\Models\Auth;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

use App\Domain\Branch\Enums\BranchRoleEnum;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Models\Business\Appointment;
use App\Models\Business\Branch;
use App\Models\Business\Business;
use App\Models\Business\Service;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
        'notify_email',
        'notify_sms',
        'is_visible',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'birth_date'        => 'date',
        'is_admin'          => 'boolean',
        'notify_email'      => 'boolean',
        'notify_sms'        => 'boolean',
        'is_visible'   => 'boolean',
    ];

    /**
     * Relations
     */
    public function businesses()
    {
        return $this->morphedByMany(Business::class, 'model', 'model_has_users')->withPivot('role');
    }

    public function branches()
    {
        return $this->morphedByMany(Branch::class, 'model', 'model_has_users')->withPivot('role');
    }

    public function services()
    {
        return $this->morphedByMany(Service::class, 'model', 'model_has_users')->withPivot('role');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Helpers
     *  */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /* public function hasBusinessRole($businessId, BusinessRoleEnum $role): bool
    {
        return $this->businesses()
            ->where('model_id', $businessId)
            ->wherePivot('role', $role->value)
            ->exists();
    }

    public function hasBranchRole($branchId, BranchRoleEnum $role): bool
    {
        return $this->branches()->where('branch_id', $branchId)->wherePivot('role', $role->value)->exists();
    } */
}
