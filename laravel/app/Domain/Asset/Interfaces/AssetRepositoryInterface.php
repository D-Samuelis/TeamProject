<?php

namespace App\Domain\Asset\Interfaces;

use App\Application\Business\DTO\SearchDTO;
use Illuminate\Support\Collection;
use App\Models\Business\Asset;
use App\Application\Asset\DTO\UpdateAssetDTO;

interface AssetRepositoryInterface
{
    public function findById(int $id): ?Asset;

    public function save(array $data): Asset;

    public function search(SearchDTO $dto): Collection;

    public function attachBranches(Asset $Asset, array $branchIds): void;

    public function attachServices(Asset $Asset, array $serviceIds): void;

    public function findForManagement(int $id): Asset;

    public function getAssignments(Asset $Asset): array;

    public function delete(Asset $asset): void;

    public function update(UpdateAssetDTO $data): Asset;
}
