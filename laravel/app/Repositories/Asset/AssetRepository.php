<?php

namespace App\Repositories\Asset;

use App\Application\DTO\SearchDTO;
use App\Application\Asset\DTO\UpdateAssetDTO;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Asset;
use Illuminate\Support\Collection;

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

    public function search(SearchDTO $dto, ?User $user = null): Collection
    {
        $query = Asset::query();

        if ($user && ! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {

                // 1. Assets linked to branches the user is directly assigned to
                $q->whereHas('branches', function ($b) use ($user) {
                    $b->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                })

                    // 2. Assets linked to services the user is directly assigned to
                    ->orWhereHas('services', function ($s) use ($user) {
                        $s->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                    })

                    // 3. Assets linked to branches that belong to a business the user manages
                    ->orWhereHas('branches.business', function ($b) use ($user) {
                        $b->whereHas('users', fn($u) =>
                        $u->where('users.id', $user->id)
                            ->whereIn('model_has_users.role', ['owner', 'manager'])
                        );
                    });
            });
        }

        return $query->get();
    }

    public function findForManagement(int $id): Asset
    {
        return Asset::withTrashed()->findOrFail($id);
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
        $asset->update(['delete_after' => now()->addDays(7)]);
        $asset->delete();
    }

    public function update(UpdateAssetDTO $data): Asset
    {
        $asset = Asset::find($data->id);
        $asset->update($data->toArray());
        return $asset;
    }
}
