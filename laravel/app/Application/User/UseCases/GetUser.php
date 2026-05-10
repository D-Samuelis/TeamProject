<?php

namespace App\Application\User\UseCases;

use App\Application\Auth\Services\UserAuthorizationService;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use App\Models\Auth\User;

class GetUser
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo,
        private readonly UserAuthorizationService $authUser
    ) {}

    public function execute(int $userId, ?User $user = null): User
    {
        $this->authUser->ensureCanViewuser($user);
        return $this->userRepo->findById($userId);
    }
}
