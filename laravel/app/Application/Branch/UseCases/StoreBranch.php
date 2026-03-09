<?php

namespace App\Application\Branch\UseCases;

use App\Application\Auth\Services\BranchAuthorizationService;
use App\Application\Branch\DTO\StoreBranchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\User\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Business\Branch;

class StoreBranch
{
    public function __construct(
        private BranchRepositoryInterface $branchRepo,
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly UserRepositoryInterface $userRepo,
        private readonly BranchAuthorizationService $authService
    ) {}

    public function execute(StoreBranchDTO $dto, int $userId): Branch
    {
        return DB::transaction(function () use ($dto, $userId) {
            $user = $this->userRepo->findById($userId);

            $business = $this->businessRepo->findActive($dto->business_id);
            
            $this->authService->ensureCanCreateBranch($user, $business);
            
            return $this->branchRepo->save($dto->toArray());
        });
    }
}