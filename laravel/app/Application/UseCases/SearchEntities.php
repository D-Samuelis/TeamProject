<?php

namespace App\Application\UseCases;

use App\Application\DTO\SearchDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;

class SearchEntities
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepo,
        private readonly BranchRepositoryInterface $branchRepo,
        private readonly ServiceRepositoryInterface $serviceRepo
    ) {}

    public function execute(SearchDTO $dto): array
    {
        $businesses = ($dto->target === 'business')
            ? $this->businessRepo->search($dto)
            : $this->businessRepo->count($dto);

        $branches = ($dto->target === 'branch')
            ? $this->branchRepo->search($dto)
            : $this->branchRepo->count($dto);

        $services = ($dto->target === 'service')
            ? $this->serviceRepo->search($dto)
            : $this->serviceRepo->count($dto);

        $results = match ($dto->target) {
            'service' => $services,
            'branch'  => $branches,
            default   => $businesses,
        };

        return [
            'businesses' => $businesses,
            'branches'   => $branches,
            'services'   => $services,
            'results'    => $results,
        ];
    }
}
