<?php

namespace App\Application\Business\UseCases;

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

    public function execute(int $businessId, int $userId): void
    {
        DB::transaction(function () use ($businessId, $userId) {
            $branch = $this->branchRepo->findById($businessId);
            $user = $this->userRepo->findById($userId);

            $this->branchAuthService->ensureCanUpdateBranch($user, $branch);

            $this->branchRepo->restore($branch);
        });
    }
}