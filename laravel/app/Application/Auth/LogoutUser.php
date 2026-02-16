<?php
// app/Application/Auth/LogoutUser.php
namespace App\Application\Auth;

use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\Auth\TokenServiceInterface;
use App\Domain\User\Entities\User as DomainUser;
use InvalidArgumentException;

/**
 * Use case class to handle user logout logic. This class encapsulates the business rules for logging out a user. It is called by the AuthController to separate concerns.
 * @package App\Application\Auth
 */

final class LogoutUser
{
    public function __construct(
        private TokenServiceInterface $tokenService,
        private UserRepository $users
    ) {}

    /**
     * Logout by domain user instance or by user id (string).
     * We'll accept a DomainUser or string id to be flexible.
     */
    public function execute(DomainUser|string $userOrId): void
    {
        if (is_string($userOrId)) {
            $user = $this->users->findById($userOrId);
            if (!$user) {
                throw new InvalidArgumentException('User not found.');
            }
        } else {
            $user = $userOrId;
        }

        $this->tokenService->revokeAllTokensFor($user);

        // other infra cleanup (optional): revoke sessions, delete refresh tokens, etc.
    }
}
