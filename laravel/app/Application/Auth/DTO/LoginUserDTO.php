<?php

namespace App\Application\Auth\DTO;

// Purpose: typed data transfer objects that Application layer uses as input/output.
final class LoginUserDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false,
    ) {}
}
