<?php

namespace App\Application\Branch\UseCases;

use App\Application\Auth\Services\BranchAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class DeleteBranch
{
    public function __construct(
        private BranchRepositoryInterface $branchRepo,
        private UserRepositoryInterface $userRepo,
        private BranchAuthorizationService $authService
    ) {}

    public function execute(int $branchId, int $userId): void
    {
        $branch = $this->branchRepo->findForManagement($branchId);

        $user = $this->userRepo->findById($userId);

        $this->authService->ensureCanDeleteBranch($user, $branch);
        
        $this->branchRepo->delete($branch);
    }
}