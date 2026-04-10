<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\DTO\UpdateUserDTO;
use App\Application\User\DTO\UpdateUserProfileDTO;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class UpdateUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $userId, UpdateUserDTO $dto)
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \RuntimeException('User not found.');
        }

        $user->updateProfile($dto);

        return $this->userRepository->save($user->toArray());
    }
}
