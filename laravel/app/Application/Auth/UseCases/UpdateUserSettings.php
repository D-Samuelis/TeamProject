<?php

namespace App\Application\Auth\UseCases;

use App\Application\Auth\DTO\UpdateUserSettingsDTO;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class UpdateUserSettings
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $userId, UpdateUserSettingsDTO $dto): void
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \RuntimeException('User not found.');
        }

        $user->fill($dto->toArray());

        $this->userRepository->update($user);
    }
}