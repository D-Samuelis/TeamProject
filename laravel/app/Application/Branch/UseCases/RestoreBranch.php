<?php

namespace App\Application\Branch\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use \App\Models\Business\Branch;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Application\Auth\Services\BranchAuthorizationService;

class RestoreBranch
{
    public function __construct(
        private readonly BranchAuthorizationService $branchAuthService,
        private readonly BranchRepositoryInterface $branchRepo
    ) {}

    /**
     * Executes the restore branch use case. It checks if the user has permission to update the branch and then restores it.
     * @param int $branchId The ID of the branch to restore.
     * @param User $user The user performing the restore operation.
     * @return Branch The restored branch instance.
     */
    public function execute(int $branchId, User $user): Branch
    {
        return DB::transaction(function () use ($branchId, $user) {
            $branch = $this->branchRepo->findForManagement($branchId);
            $this->branchAuthService->ensureCanUpdateBranch($user, $branch);
            return $this->branchRepo->restore($branch);
        });
    }
}
