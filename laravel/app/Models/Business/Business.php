<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Domain\Business\Enums\BusinessStateEnum;
use App\Models\Auth\User;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'state',
        'is_published',
    ];

    protected $casts = [
        'state' => BusinessStateEnum::class,
        'is_published' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function isOwner(int $userId): bool
    {
        return $this->owner_id === $userId;
    }

    public function canCreateBranch(int $userId): bool
    {
        return $this->isOwner($userId);
    }
}
