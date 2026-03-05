<?php

namespace App\Infrastructure\Auth;

use App\Models\Auth\User;

interface TokenServiceInterface
{
    public function createTokenFor(User $user): string;
    public function revokeAllTokensFor(User $user): void;
}
