<?php

namespace App\Application\Business\UseCases;

use App\Application\Business\DTO\UpdateBusinessDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Business\Business;

class UpdateBusiness
{
    public function __construct(
        private BusinessRepositoryInterface $businessRepo
    ) {}

    /**
     * Update the business details.
     * Note: Branches and Services are handled by their own UseCases.
     */
    public function execute(UpdateBusinessDTO $dto, int $userId): void
    {
        $this->businessRepo->update($dto);
    }
}