<?php

namespace App\Application\Branch\UseCases;

use App\Application\Business\DTO\SearchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;

class ListBranches
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepo
    ) {}

    public function execute(array $filters = [])
    {
        $dto = SearchDTO::fromArray($filters);
        return $this->branchRepo->search($dto);
    }
}
