<?php

namespace App\Domain\User\Services;

interface PasswordHasher
{
    public function hash(string $plain): string;

    public function verify(string $plain, string $hashed): bool;
}
