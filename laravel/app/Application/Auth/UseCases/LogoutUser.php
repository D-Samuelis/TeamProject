<?php

namespace App\Application\Auth\UseCases;

use InvalidArgumentException;
use App\Repositories\Auth\TokenServiceInterface;
use App\Models\Auth\User;

final class LogoutUser
{
    public function __construct(
        private TokenServiceInterface $tokenService,
    ) {}

    public function execute(User $user): void
    {
        if (!$user) {
            throw new InvalidArgumentException('User not found.');
        }

        $this->tokenService->revokeAllTokensFor($user);
    }
}
