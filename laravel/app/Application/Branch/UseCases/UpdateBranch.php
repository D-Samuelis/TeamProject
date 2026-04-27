<?php

namespace App\Application\Branch\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Business\Branch;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Application\Auth\Services\BranchAuthorizationService;
use App\Application\Branch\DTO\UpdateBranchDTO;

class UpdateBranch
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly BranchAuthorizationService $authService
    ) {}

    /**
     * Executes the update branch use case. It checks if the user has permission to update the branch and then updates it with the provided data.
     * @param UpdateBranchDTO $dto The data transfer object containing the branch ID and the data to update.
     * @param User $user The user performing the update operation.
     * @return Branch The updated branch.
     */
    public function execute(UpdateBranchDTO $dto, User $user): Branch
    {
        return DB::transaction(function () use ($dto, $user) {
            $branch = $this->branchRepo->findForManagement($dto->id);
            $this->authService->ensureCanUpdateBranch($user, $branch);
            return $this->branchRepo->update($branch, $dto->toArray());
        });
    }
}
