<?php

namespace App\Repositories\Auth;

use Illuminate\Support\Facades\Hash;
use App\Domain\User\Services\PasswordHasher;

final class LaravelPasswordHasher implements PasswordHasher
{
    public function hash(string $plain): string
    {
        return Hash::make($plain);
    }

    public function verify(string $plain, string $hashed): bool
    {
        return Hash::check($plain, $hashed);
    }
}
