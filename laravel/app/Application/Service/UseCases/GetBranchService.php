<?php

namespace App\Application\Service\UseCases;

use App\Domain\Service\Interfaces\BranchServiceRepositoryInterface;
use App\Models\Business\BranchService;

class GetBranchService
{
    public function __construct(
        private readonly BranchServiceRepositoryInterface $repository
    ) {}

    public function execute(int $id): BranchService
    {
        return $this->repository->findActive($id);
    }
}