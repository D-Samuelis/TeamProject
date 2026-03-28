<?php

namespace App\Application\Branch\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Auth\Services\BranchAuthorizationService;
use App\Application\Branch\DTO\StoreBranchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Branch;

class StoreBranch
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BranchAuthorizationService $authService
    ) {}

    public function execute(StoreBranchDTO $dto, User $user): Branch
    {
        return DB::transaction(function () use ($dto, $user) {
            $business = $this->businessRepo->findForManagement($dto->business_id);
            
            $this->authService->ensureCanCreateBranch($user, $business);
            
            return $this->branchRepo->save($dto->toArray());
        });
    }
}