<?php

namespace App\Application\Business\UseCases;

use App\Domain\Business\Repositories\BusinessRepositoryInterface;

class ListBusinesses
{
    public function __construct(
        private readonly BusinessRepositoryInterface $businessRepository
    ) {}

    public function execute()
    {
        return $this->businessRepository->allWithRelations();
    }
}
