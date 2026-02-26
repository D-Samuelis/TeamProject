<?php
// app/Application/Auth/LoginUser.php
namespace App\Application\Auth\UseCases;

use App\Application\Auth\DTO\LoginUserDTO;
use App\Application\Auth\DTO\RegisteredUserDTO;
use App\Domain\User\Repositories\UserRepository;
use App\Infrastructure\Auth\TokenServiceInterface;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

/**
 * Use case class to handle user login logic. This class encapsulates the business rules for logging in a user. It is called by the AuthController to separate concerns.
 * @package App\Application\Auth
 */
final class LoginUser
{
    public function __construct(
        private UserRepository $users,
        private TokenServiceInterface $tokenService
    ) {}

    /**
     * @throws InvalidArgumentException on invalid credentials
     */
    public function execute(LoginUserDTO $dto): RegisteredUserDTO
    {
        $user = $this->users->findByEmail($dto->email);
        if (!$user) {
            throw new InvalidArgumentException('Invalid credentials.');
        }

        if (!Hash::check($dto->password, $user->passwordHash)) {
            throw new InvalidArgumentException('Invalid credentials.');
        }

        // Optionally: check domain rules (locked, banned)
        // if ($user->isLocked()) { throw new DomainException(...); }

        $token = $this->tokenService->createTokenFor($user);

        return new RegisteredUserDTO($user, $token);
    }
}
