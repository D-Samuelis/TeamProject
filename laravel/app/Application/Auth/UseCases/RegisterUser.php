<?php

namespace App\Application\Auth\UseCases;

use InvalidArgumentException;
use App\Application\Auth\DTO\RegisteredUserDTO;
use App\Application\Auth\DTO\RegisterUserDTO;
use App\Repositories\Auth\TokenServiceInterface;
use App\Domain\User\Services\PasswordHasher;
use App\Domain\User\Interfaces\UserRepositoryInterface;

/**
 * Use case class to handle user registration logic.
 */
final class RegisterUser
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private TokenServiceInterface $tokenService,
        private PasswordHasher $hasher
    ) {}

    public function execute(RegisterUserDTO $dto): RegisteredUserDTO
    {
        if ($this->userRepo->findByEmail($dto->email)) {
            throw new InvalidArgumentException('Email already registered.');
        }

        $dto->password = $this->hasher->hash($dto->password);

        $user = $this->userRepo->save($dto->toArray());

        $token = $this->tokenService->createTokenFor($user);

        return new RegisteredUserDTO($user, $token);
    }
}
