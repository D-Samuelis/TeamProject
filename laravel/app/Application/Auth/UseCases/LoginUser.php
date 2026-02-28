<?php

namespace App\Application\Auth\UseCases;

use InvalidArgumentException;

use App\Infrastructure\Auth\TokenServiceInterface;
use App\Domain\User\Services\PasswordHasher;

use App\Application\Auth\DTO\LoginUserDTO;
use App\Application\Auth\DTO\RegisteredUserDTO;
use App\Domain\User\Repositories\UserRepositoryInterface;

/**
 * Use case class to handle user login logic.
 */
final class LoginUser
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private PasswordHasher $hasher,
        private TokenServiceInterface $tokenService
    ) {}

    public function execute(LoginUserDTO $dto): RegisteredUserDTO
    {
        $user = $this->userRepo->findByEmail($dto->email);

        if (!$user || !$this->hasher->verify($dto->password, $user->password)) {
            throw new InvalidArgumentException('Invalid credentials.');
        }

        $token = $this->tokenService->createTokenFor($user);
        return new RegisteredUserDTO($user, $token);
    }
}