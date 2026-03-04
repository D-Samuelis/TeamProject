<?php

namespace App\Models\Business;

use App\Domain\Business\Enums\BusinessRoleEnum;
use Illuminate\Database\Eloquent\Model;

class BusinessMembership extends Model
{
    protected $table = 'business_user';

    protected $fillable = [
        'business_id',
        'user_id',
        'role',
    ];

    protected $casts = [
        'role' => BusinessRoleEnum::class,
    ];
}