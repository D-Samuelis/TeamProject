<?php

namespace App\Infrastructure\Auth;

interface TokenServiceInterface
{
    public function createTokenFor(\App\Domain\User\Entities\User $user): string;
    public function revokeAllTokensFor(\App\Domain\User\Entities\User $user): void;
}
