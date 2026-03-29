<?php

namespace App\Application\Branch\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Auth\Services\BranchAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Models\Auth\User;

class RestoreBranch
{
    public function __construct(
        private readonly BranchAuthorizationService $branchAuthService,
        private readonly BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(int $branchId, User $user): void
    {
        DB::transaction(function () use ($branchId, $user) {
            $branch = $this->branchRepo->findForManagement($branchId);

            $this->branchAuthService->ensureCanUpdateBranch($user, $branch);

            $this->branchRepo->restore($branch);
        });
    }
}
