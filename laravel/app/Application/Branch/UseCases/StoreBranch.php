<?php

namespace App\Application\Branch\UseCases;

use Illuminate\Support\Facades\DB;
use App\Models\Auth\User;
use App\Models\Business\Branch;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Application\Auth\Services\BranchAuthorizationService;
use App\Application\Branch\DTO\StoreBranchDTO;

class StoreBranch
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BranchAuthorizationService $authService
    ) {}

    /**
     * Executes the store branch use case. It checks if the user has permission to create a branch and then creates it with the provided data.
     * @param StoreBranchDTO $dto The data transfer object containing the data for the new branch.
     * @param User $user The user performing the create operation.
     * @return Branch The created branch.
     */
    public function execute(StoreBranchDTO $dto, User $user): Branch
    {
        return DB::transaction(function () use ($dto, $user) {
            $business = $this->businessRepo->findForManagement($dto->business_id);            
            $this->authService->ensureCanCreateBranch($user, $business);            
            return $this->branchRepo->save($dto->toArray());
        });
    }
}