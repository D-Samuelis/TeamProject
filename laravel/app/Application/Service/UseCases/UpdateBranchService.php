<?php

namespace App\Application\Service\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Auth\Services\ServiceAuthorizationService;
use App\Application\Service\DTO\UpdateBranchServiceDTO;
use App\Domain\Service\Interfaces\BranchServiceRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\BranchService;

class UpdateBranchService
{
    public function __construct(
        private readonly BranchServiceRepositoryInterface $branchServiceRepo,
        private readonly ServiceAuthorizationService      $authService,
    ) {}

    public function execute(UpdateBranchServiceDTO $dto, User $user): BranchService
    {
        return DB::transaction(function () use ($dto, $user) {
            $branchService = $this->branchServiceRepo->findForManagement($dto->id);

            $this->authService->ensureCanUpdateService($user, $branchService->service);

            $data = $dto->toArray();

            if (empty($data)) {
                return $branchService; // Nothing to update
            }

            return $this->branchServiceRepo->updateInstance($branchService, $data);
        });
    }
}
