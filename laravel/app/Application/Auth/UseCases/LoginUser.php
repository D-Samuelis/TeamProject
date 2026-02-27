<?php

namespace App\Application\Auth\UseCases;

use InvalidArgumentException;

use App\Infrastructure\Auth\TokenServiceInterface;
use App\Domain\User\Services\PasswordHasher;
use App\Domain\User\Entities\User;

use App\Application\Auth\DTO\LoginUserDTO;
use App\Application\Auth\DTO\RegisteredUserDTO;

/**
 * Use case class to handle user login logic.
 */
final class LoginUser
{
    public function __construct(
        private TokenServiceInterface $tokenService,
        private PasswordHasher $hasher
    ) {}

    public function execute(LoginUserDTO $dto): RegisteredUserDTO
    {
        $user = User::where('email', $dto->email)->first();

        if (!$user || !$this->hasher->verify($dto->password, $user->password)) {
            throw new InvalidArgumentException('Invalid credentials.');
        }

        $token = $this->tokenService->createTokenFor($user);

        return new RegisteredUserDTO($user, $token);
    }
}
