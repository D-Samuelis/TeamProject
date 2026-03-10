<?php

namespace App\Application\Branch\UseCases;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Branch;

class GetBranch
{
    public function __construct(private readonly BranchRepositoryInterface $branchRepo) {}

    public function execute(int $branchId): Branch
    {
        return $this->branchRepo->findForManagement($branchId);
    }
}
