<?php

namespace App\Application\Business\UseCases;

use App\Domain\Business\Repositories\BusinessRepositoryInterface;

class ListBusinesses
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepository
    ) {}

    public function execute(string $scope = 'active')
    {
        return $this->businessRepository->allWithRelations($scope);
    }
}
