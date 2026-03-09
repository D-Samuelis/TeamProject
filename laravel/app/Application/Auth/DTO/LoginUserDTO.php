<?php

namespace App\Application\Auth\DTO;

final class LoginUserDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false,
    ) {}
}
