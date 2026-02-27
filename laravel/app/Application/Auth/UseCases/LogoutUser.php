<?php

namespace App\Application\Auth\UseCases;

use App\Infrastructure\Auth\TokenServiceInterface;
use App\Domain\User\Entities\User;
use InvalidArgumentException;

/**
 * Use case class to handle user logout logic.
 */
final class LogoutUser
{
    public function __construct(
        private TokenServiceInterface $tokenService
    ) {}

    /**
     * Logout by user instance or user id.
     */
    public function execute(User|string $userOrId): void
    {
        if (is_string($userOrId)) {
            $user = User::find($userOrId);
            if (!$user) {
                throw new InvalidArgumentException('User not found.');
            }
        } else {
            $user = $userOrId;
        }

        $this->tokenService->revokeAllTokensFor($user);

        // revoke sessions (?)
    }
}
