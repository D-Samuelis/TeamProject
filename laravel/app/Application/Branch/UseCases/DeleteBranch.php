<?php

namespace App\Application\Branch\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Auth\Services\BranchAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Models\Auth\User;

class DeleteBranch
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly BranchAuthorizationService $authService
    ) {}

    public function execute(int $branchId, User $user): void
    {
        DB::transaction(function () use ($branchId, $user) {
            $branch = $this->branchRepo->findForManagement($branchId);

            $this->authService->ensureCanDeleteBranch($user, $branch);

            $this->branchRepo->delete($branch);
        });
    }
}
