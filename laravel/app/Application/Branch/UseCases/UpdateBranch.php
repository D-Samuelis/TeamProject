<?php

namespace App\Application\Branch\UseCases;

use App\Application\Auth\Services\BranchAuthorizationService;
use App\Application\Branch\DTO\UpdateBranchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class UpdateBranch
{
    public function __construct(
        private BranchRepositoryInterface $branchRepo,
        private UserRepositoryInterface $userRepo,
        private BranchAuthorizationService $authService
    ) {}

    public function execute(UpdateBranchDTO $dto, int $userId): void
    {
        $branch = $this->branchRepo->findById($dto->id);
        $user = $this->userRepo->findById($userId);

        $this->authService->ensureCanUpdateBranch($user, $branch);
        
        $this->branchRepo->update($dto->id, $dto->toArray());
    }
}