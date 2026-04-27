<?php

namespace App\Application\Branch\UseCases;

use App\Models\Auth\User;
use App\Models\Business\Branch;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Application\Auth\Services\BranchAuthorizationService;

class GetBranch
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly BranchAuthorizationService $authService
    ) {}

    /**
     * Executes the get branch use case. It checks if the user has permission to view the branch and returns it if found.
     * @param int $branchId The ID of the branch to retrieve.
     * @param User|null $user The user requesting the branch, or null for unauthenticated access.
     * @return Branch The retrieved branch.
     */
    public function execute(int $branchId, ?User $user = null): Branch
    {
        if ($user) {
            $branch = $this->branchRepo->findForManagement($branchId);
            $this->authService->ensureCanViewBranch($user, $branch);
            return $branch;
        }

        $branch = $this->branchRepo->findActive($branchId);
        return $branch;
    }
}
