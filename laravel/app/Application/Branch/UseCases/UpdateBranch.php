<?php

namespace App\Application\Branch\UseCases;

use Illuminate\Support\Facades\DB;
use App\Application\Auth\Services\BranchAuthorizationService;
use App\Application\Branch\DTO\UpdateBranchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Branch;

class UpdateBranch
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly BranchAuthorizationService $authService
    ) {}

    public function execute(UpdateBranchDTO $dto, User $user): Branch
    {
        return DB::transaction(function () use ($dto, $user) {
            $branch = $this->branchRepo->findForManagement($dto->id);
            $this->authService->ensureCanUpdateBranch($user, $branch);
            return $this->branchRepo->update($branch, $dto->toArray());
        });
    }
}
