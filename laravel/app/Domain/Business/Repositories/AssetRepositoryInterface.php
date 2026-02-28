<?php

namespace App\Domain\Business\Repositories;

use App\Domain\Business\Entities\Asset;

interface AssetRepositoryInterface
{
    public function findById(int $id): ?Asset;

    public function findByBranchId(int $branchId): array;

    public function findByServiceId(int $serviceId): array;

    public function create(array $data): Asset;
}
