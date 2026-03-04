<?php

namespace App\Application\Auth\DTO;

final class RegisteredUserDTO
{
    public function __construct(
        public \App\Domain\User\Entities\User $user,
        public ?string $token = null
    ) {}
}
