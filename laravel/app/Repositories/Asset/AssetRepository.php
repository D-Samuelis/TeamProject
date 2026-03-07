<?php

namespace App\Repositories\Asset;

use Illuminate\Support\Collection;
use App\Models\Business\Asset;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Application\Asset\DTO\UpdateAssetDTO;

class AssetRepository implements AssetRepositoryInterface
{
    public function findById(int $id): ?Asset
    {
        return Asset::find($id);
    }

    public function save(array $data): Asset
    {
        return Asset::create($data);
    }

    public function attachServices(Asset $asset, array $serviceIds): void
    {
        $asset->services()->sync($serviceIds);
    }

    public function attachBranches(Asset $asset, array $branchIds): void
    {
        $asset->branches()->sync($branchIds);
    }

    public function getAssignments(Asset $asset): array
    {
        return [
            'services' => $asset->services()->pluck('id')->all(),
            'branches' => $asset->branches()->pluck('id')->all(),
        ];
    }

    public function delete(Asset $asset): void
    {
        $asset->update([
            'delete_after' => now()->addDays(7)
        ]);

        $asset->delete();
    }

    public function update(UpdateAssetDTO $data): Asset
    {
        $asset = Asset::find($data->id);

        $asset->update($data->toArray());

        return $asset;
    }
}
