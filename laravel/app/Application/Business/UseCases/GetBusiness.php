<?php

namespace App\Application\Business\UseCases;

use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Business\Business;

class GetBusiness
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo
    ) {}

    public function execute(int $businessId): Business
    {
        return $this->businessRepo->findByIdWithRelations($businessId);
    }
}