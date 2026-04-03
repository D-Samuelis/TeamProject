<?php

namespace App\Repositories\Service;

use Illuminate\Database\Eloquent\Builder;
use App\Application\DTO\SearchDTO;
use App\Models\Business\BranchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use \App\Domain\Service\Interfaces\BranchServiceRepositoryInterface;

class BranchServiceRepository implements BranchServiceRepositoryInterface
{
    /**
     * PUBLIC: End-user marketplace search.
     * Drives the explore/search page — returns enabled branch service instances
     * with full branch and business context.
     */
    public function search(SearchDTO $dto): LengthAwarePaginator
    {
        $query = BranchService::query()
            ->where('is_enabled', true)
            ->whereHas(
                'branch',
                fn($q) =>
                $q->where('is_active', true)
                    ->whereHas('business', fn($q) => $q->where('is_published', true))
            );

        $this->applyFilters($query, $dto);

        return $query
            ->with([
                'service',
                'branch.business',
                'assets',
            ])
            ->latest()
            ->paginate($dto->perPage);
    }

    public function count(SearchDTO $dto): int
    {
        $query = BranchService::query()
            ->where('is_enabled', true)
            ->whereHas(
                'branch',
                fn($q) =>
                $q->where('is_active', true)
                    ->whereHas('business', fn($q) => $q->where('is_published', true))
            );

        $this->applyFilters($query, $dto);

        return $query->count();
    }

    public function findActive(int $id): BranchService
    {
        return BranchService::query()
            ->where('is_enabled', true)
            ->with([
                'service',          // The template (name, base description)
                'assets',           // The specific rooms/equipment for this branch
                'branch.business'   // The location and owner info
            ])
            ->findOrFail($id);
    }

    /**
     * MANAGEMENT: Create a branch service instance from a template.
     */
    public function createInstance(array $data): BranchService
    {
        return BranchService::create($data);
    }

    public function updateInstance(BranchService $branchService, array $data): BranchService
    {
        $branchService->update($data);
        return $branchService->fresh();
    }

    public function deleteInstance(BranchService $branchService): void
    {
        $branchService->update(['is_enabled' => false]);
        $branchService->delete();
    }

    public function findById(int $id): ?BranchService
    {
        return BranchService::find($id);
    }

    public function findForBranch(int $branchId, bool $enabledOnly = true): \Illuminate\Support\Collection
    {
        $query = BranchService::where('branch_id', $branchId)
            ->with(['service', 'assets']);

        if ($enabledOnly) {
            $query->where('is_enabled', true);
        }

        return $query->get();
    }

    public function findWithinBranch(int $branchServiceId, int $branchId): BranchService
    {
        return BranchService::where('id', $branchServiceId)
            ->where('branch_id', $branchId)
            ->firstOrFail();
    }

    /**
     * PRIVATE HELPERS
     */
    private function applyFilters(Builder $query, SearchDTO $dto): void
    {
        if ($dto->query) {
            $keyword = $dto->query;
            $query->where(function ($sub) use ($keyword) {
                $sub->whereHas(
                    'service',
                    fn($s) =>
                    $s->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                )
                    ->orWhereHas(
                        'branch',
                        fn($b) =>
                        $b->where('name', 'like', "%{$keyword}%")
                            ->orWhere('city', 'like', "%{$keyword}%")
                            ->orWhereHas(
                                'business',
                                fn($bus) =>
                                $bus->where('name', 'like', "%{$keyword}%")
                            )
                    );
            });
        }

        if ($dto->city) {
            $query->whereHas('branch', fn($q) => $q->where('city', $dto->city));
        }

        if ($dto->businessId) {
            $query->whereHas('branch', fn($q) => $q->where('business_id', $dto->businessId));
        }

        // Filter on effective price: prefer custom_price, fall back to base_price
        if ($dto->maxPrice) {
            $query->where(function ($q) use ($dto) {
                $q->where(fn($q) => $q->whereNotNull('custom_price')->where('custom_price', '<=', $dto->maxPrice))
                    ->orWhere(
                        fn($q) => $q->whereNull('custom_price')
                            ->whereHas('service', fn($s) => $s->where('base_price', '<=', $dto->maxPrice))
                    );
            });
        }

        if ($dto->maxDuration) {
            $query->where(function ($q) use ($dto) {
                $q->where(fn($q) => $q->whereNotNull('custom_duration_minutes')->where('custom_duration_minutes', '<=', $dto->maxDuration))
                    ->orWhere(
                        fn($q) => $q->whereNull('custom_duration_minutes')
                            ->whereHas('service', fn($s) => $s->where('base_duration_minutes', '<=', $dto->maxDuration))
                    );
            });
        }

        if (! empty($dto->locationTypes)) {
            $query->where(function ($q) use ($dto) {
                $q->where(fn($q) => $q->whereNotNull('location_type')->whereIn('location_type', $dto->locationTypes))
                    ->orWhere(
                        fn($q) => $q->whereNull('location_type')
                            ->whereHas('service', fn($s) => $s->whereIn('location_type', $dto->locationTypes))
                    );
            });
        }
    }
}
