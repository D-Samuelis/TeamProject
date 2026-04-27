<?php

namespace App\Application\Branch\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Application\Auth\Services\BranchAuthorizationService;

class DeleteBranch
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly BranchAuthorizationService $authService
    ) {}

    /**
     * Executes the delete branch use case. It checks if the user has permission to delete the branch and then deletes it.
     * @param int $branchId The ID of the branch to delete.
     * @param User $user The user performing the delete operation.
     * @return void
     */
    public function execute(int $branchId, User $user): void
    {
        DB::transaction(function () use ($branchId, $user) {
            $branch = $this->branchRepo->findForManagement($branchId);
            $this->authService->ensureCanDeleteBranch($user, $branch);
            $this->branchRepo->delete($branch);
        });
    }
}
