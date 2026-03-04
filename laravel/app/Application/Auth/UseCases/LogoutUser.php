<?php

namespace App\Application\Auth\UseCases;

use InvalidArgumentException;

use App\Domain\User\Entities\User; 
use App\Domain\User\Repositories\UserRepositoryInterface;

use App\Infrastructure\Auth\TokenServiceInterface;

final class LogoutUser
{
    public function __construct(
        private TokenServiceInterface $tokenService,
        private UserRepositoryInterface $userRepo
    ) {}

    public function execute(User|int|string $userOrId): void
    {
        if (is_string($userOrId) || is_int($userOrId)) {
            $user = $this->userRepo->findById((int)$userOrId);
            if (!$user) {
                throw new InvalidArgumentException('User not found.');
            }
        } else {
            $user = $userOrId;
        }

        $this->tokenService->revokeAllTokensFor($user);
    }
}