<?php

namespace App\Application\Branch\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Auth\Services\BranchAuthorizationService;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;

class RestoreBranch
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private BranchAuthorizationService $branchAuthService,
        private BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(int $branchId, int $userId): void
    {
        DB::transaction(function () use ($branchId, $userId) {
            $branch = $this->branchRepo->findForManagement($branchId);
            
            $user = $this->userRepo->findById($userId);

            $this->branchAuthService->ensureCanUpdateBranch($user, $branch);

            $this->branchRepo->restore($branch);
        });
    }
}
