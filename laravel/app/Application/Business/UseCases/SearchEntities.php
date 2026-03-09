<?php

namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\SearchDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use Illuminate\Support\Collection;

class SearchEntities
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly ServiceRepositoryInterface $serviceRepo
    ) {}

    /**
     * Route the search request to the appropriate repository 
     * based on the target entity type.
     */
    public function execute(SearchDTO $dto): Collection
    {
        return match ($dto->target) {
            'service' => $this->serviceRepo->search($dto),
            'branch'  => $this->branchRepo->search($dto),
            'business' => $this->businessRepo->search($dto),
            default   => $this->businessRepo->search($dto),
        };
    }
}
