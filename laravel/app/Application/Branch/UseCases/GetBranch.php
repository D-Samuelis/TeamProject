<?php

namespace App\Application\Branch\UseCases;

use App\Application\Auth\Services\BranchAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Branch;

class GetBranch
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly BranchAuthorizationService $authService
    ) {}

    public function execute(int $branchId, ?User $user = null): Branch
    {
        if ($user) {
            $branch = $this->branchRepo->findForManagement($branchId);
            $this->authService->ensureCanViewBranch($user, $branch);
            return $branch;
        }

        return $this->branchRepo->findActive($branchId);
    }
}
