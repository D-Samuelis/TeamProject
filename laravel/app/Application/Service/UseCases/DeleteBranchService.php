<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Domain\Service\Interfaces\BranchServiceRepositoryInterface;
use App\Models\Auth\User;

class DeleteBranchService
{
    public function __construct(
        private readonly BranchServiceRepositoryInterface $branchServiceRepo,
        private readonly ServiceAuthorizationService      $authService,
    ) {}

    public function execute(int $branchServiceId, User $user): void
    {
        DB::transaction(function () use ($branchServiceId, $user) {
            $branchService = $this->branchServiceRepo->findForManagement($branchServiceId);

            $this->authService->ensureCanUpdateService($user, $branchService->service);

            $this->branchServiceRepo->deleteInstance($branchService);
        });
    }
}
