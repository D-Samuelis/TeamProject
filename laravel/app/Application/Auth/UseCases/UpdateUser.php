<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\DTO\UpdateUserDTO;
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

        $user->fill($dto->toArray());

        return $this->userRepository->update($user);
    }
}
