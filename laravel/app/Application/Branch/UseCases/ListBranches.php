<?php

namespace App\Application\Branch\UseCases;

use App\Domain\Branch\Interfaces\BranchRepositoryInterface;

class ListBranches
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepository
    ) {}

    public function execute(string $scope = 'active')
    {
        return $this->branchRepository->allWithRelations($scope);
    }
}