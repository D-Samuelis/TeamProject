<?php

namespace App\Application\User\UseCases;

use App\Application\DTO\UserSearchDTO;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\Auth\User;

class ListUsers
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo,
    ) {}

    public function execute(UserSearchDTO $dto, ?User $user = null)
    {
        return $this->userRepo->search($dto, $user);
    }
}
