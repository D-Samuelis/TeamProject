<?php

namespace App\Application\Auth\UseCases;

use InvalidArgumentException;

use App\Application\Auth\DTO\RegisteredUserDTO;
use App\Application\Auth\DTO\RegisterUserDTO;

use App\Domain\User\Services\PasswordHasher;
use App\Domain\User\Repositories\UserRepositoryInterface;

use App\Infrastructure\Auth\TokenServiceInterface;

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
        // Check if email exists
        if ($this->userRepo->findByEmail($dto->email)) {
            throw new InvalidArgumentException('Email already registered.');
        }

        // Create domain entity
        $user = new \App\Domain\User\Entities\User(
            null,
            $dto->name,
            $dto->email,
            $this->hasher->hash($dto->password),
            $dto->country,
            $dto->city,
            $dto->title_prefix,
            $dto->birth_date ? new \DateTimeImmutable($dto->birth_date) : null,
            $dto->title_suffix,
            $dto->phone_number,
            $dto->gender
        );

        // Persist via repository
        $this->userRepo->save($user);

        // Generate token
        $token = $this->tokenService->createTokenFor($user);

        return new RegisteredUserDTO($user, $token);
    }
}
