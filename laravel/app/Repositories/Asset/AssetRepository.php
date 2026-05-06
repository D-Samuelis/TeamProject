<?php

namespace App\Repositories\Asset;

use App\Application\DTO\AssetSearchDTO;
use App\Application\DTO\SearchDTO;
use App\Application\Asset\DTO\UpdateAssetDTO;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use App\Models\Auth\User;
use App\Models\Business\Asset;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

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

    public function search(AssetSearchDTO $dto, ?User $user = null)
    {
        $query = Asset::withTrashed();

        if ($user && !$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('branch', function ($b) use ($user) {
                    $b->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                })
                    ->orWhereHas('services', function ($s) use ($user) {
                        $s->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                    })
                    ->orWhereHas('branch.business', function ($b) use ($user) {
                        $b->whereHas('users', function ($u) use ($user) {
                            $u->where('users.id', $user->id)
                                ->whereIn('model_has_users.role', ['owner', 'manager']);
                        });
                    });
            });
        }

        if ($dto->statuses && in_array('deleted', $dto->statuses)) {
            $query->onlyTrashed();
        }

        if ($dto->statuses && in_array('active', $dto->statuses)) {
            $query->where('is_active', true);
        }

        if ($dto->statuses && in_array('inactive', $dto->statuses)) {
            $query->where('is_active', false);
        }

        if ($dto->assetName) {
            $query->where('name', 'like', '%' . $dto->assetName . '%');
        }

        if ($dto->description) {
            $query->where('description', 'like', '%' . $dto->description . '%');
        }

        if ($dto->serviceId) {
            $query->whereHas('services', fn($s) => $s->where('services.id', $dto->serviceId));
        }

        return $query->latest()->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    public function publicSearch(SearchDTO $dto, ?User $user = null)
    {
        $query = Asset::withTrashed();

        if ($user && !$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('branch', function ($b) use ($user) {
                    $b->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                })
                    ->orWhereHas('services', function ($s) use ($user) {
                        $s->whereHas('users', fn($u) => $u->where('users.id', $user->id));
                    })
                    ->orWhereHas('branch.business', function ($b) use ($user) {
                        $b->whereHas('users', function ($u) use ($user) {
                            $u->where('users.id', $user->id)
                                ->whereIn('model_has_users.role', ['owner', 'manager']);
                        });
                    });
            });
        }

        $this->applySearchFilters($query, $dto);

        return $query->latest()->paginate($dto->perPage);
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
            'branch_id' => $asset->branch_id,
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

    public function restore(Asset $asset): void
    {
        $asset->restore();

        $asset->update(['delete_after' => null]);
    }

    public function findActive(int $id): Asset
    {
        return Asset::query()
            ->where('is_active', true)
            ->whereHas('branch', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('business', function ($q) {
                        $q->where('is_published', true);
                    });
            })
            ->findOrFail($id);
    }

    private function applySearchFilters(Builder $query, SearchDTO $dto): void
    {
        if ($dto->query) {
            $keyword = $dto->query;
            $query->where(function ($sub) use ($keyword) {
                $sub->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('branch', fn($b) => $b->where('name', 'like', "%{$keyword}%"))
                    ->orWhereHas('services', fn($s) => $s->where('name', 'like', "%{$keyword}%"));
            });
        }

        if ($dto->city) {
            $query->whereHas('branch', fn($q) => $q->where('city', $dto->city));
        }
    }
}
