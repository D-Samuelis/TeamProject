<?php

namespace App\Application\Auth\DTO;

use App\Models\Auth\User;

final class RegisteredUserDTO
{
    public function __construct(
        public User $user,
        public ?string $token = null
    ) {}
}
