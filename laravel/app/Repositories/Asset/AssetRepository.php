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

                // Assets linked to branches the user is assigned to
                $q->whereHas('branches', function ($b) use ($user) {
                    $b->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                })

                // Assets linked to branch service instances the user's branch covers
                ->orWhereHas('branchServices.branch', function ($b) use ($user) {
                    $b->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                })

                // Assets linked to branches under a business the user owns/manages
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

    /**
     * Attach asset to branch service instances (replaces old attachServices).
     */
    public function attachBranchServices(Asset $asset, array $branchServiceIds): void
    {
        $asset->branchServices()->sync($branchServiceIds);
    }

    public function attachBranches(Asset $asset, array $branchIds): void
    {
        $asset->branches()->sync($branchIds);
    }

    public function getAssignments(Asset $asset): array
    {
        return [
            'branch_services' => $asset->branchServices()->pluck('id')->all(),
            'branches'        => $asset->branches()->pluck('id')->all(),
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
