<?php

namespace App\Application\User\UseCases;

use App\Application\User\DTO\UpdateUserProfileDTO;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Entities\User as DomainUser;

class UpdateUserProfile
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $userId, UpdateUserProfileDTO $dto): DomainUser
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \RuntimeException('User not found.');
        }

        $user->updateProfile($dto);

        return $this->userRepository->save($user);
    }
}