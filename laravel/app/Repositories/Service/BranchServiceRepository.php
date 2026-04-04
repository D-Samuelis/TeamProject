<?php

namespace App\Repositories\Service;

use Illuminate\Database\Eloquent\Builder;
use App\Application\DTO\SearchDTO;
use App\Domain\Branch\Enums\BranchRoleEnum;
use App\Models\Auth\User;
use App\Models\Business\Branch;
use App\Models\Business\BranchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use \App\Domain\Service\Interfaces\BranchServiceRepositoryInterface;

class BranchServiceRepository implements BranchServiceRepositoryInterface
{
    // ── PUBLIC: End-user marketplace ─────────────────────────────

    /**
     * Drives the explore/search page — returns enabled branch service instances
     * with full branch and business context.
     */
    public function search(SearchDTO $dto): LengthAwarePaginator
    {
        $query = BranchService::query()
            ->where('is_enabled', true)
            ->whereHas(
                'branch',
                fn($q) => $q->where('is_active', true)
                    ->whereHas('business', fn($q) => $q->where('is_published', true))
            );

        $this->applyFilters($query, $dto);

        return $query
            ->with(['service', 'branch.business', 'assets'])
            ->latest()
            ->paginate($dto->perPage);
    }

    public function count(SearchDTO $dto): int
    {
        $query = BranchService::query()
            ->where('is_enabled', true)
            ->whereHas(
                'branch',
                fn($q) => $q->where('is_active', true)
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
                'service',        // Template: name, base description
                'assets',         // Rooms/equipment for this branch instance
                'branch.business' // Location and owner info
            ])
            ->findOrFail($id);
    }

    // ── MANAGEMENT: Staff-facing operations ──────────────────────

    /**
     * List branch service instances visible to a given user.
     *
     * Access is granted at three levels (any is sufficient):
     *   1. Business owner/manager   — sees all instances across all branches
     *   2. Branch manager/staff     — sees all instances for their branch
     *   3. Directly assigned        — sees only their assigned instance(s)
     */
    public function listForUser(User $user, ?Branch $branch = null, string $scope = 'active'): Collection
    {
        $query = BranchService::query();

        if ($branch) {
            $query->where('branch_id', $branch->id);
        }

        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('branch.business.users', fn($sub) => $sub->where('user_id', $user->id))
                  ->orWhereHas('branch.users', fn($sub) => $sub->where('user_id', $user->id))
                  ->orWhereHas('users', fn($sub) => $sub->where('user_id', $user->id));
            });
        }

        match ($scope) {
            'disabled' => $query->where('is_enabled', false),
            'all'      => $query,
            default    => $query->where('is_enabled', true),
        };

        return $query
            ->with(['service', 'branch.business', 'assets', 'users'])
            ->latest()
            ->get();
    }

    /**
     * Full management view of a single instance, including soft-deleted.
     */
    public function findForManagement(int $id): BranchService
    {
        return BranchService::withTrashed()
            ->with(['service', 'branch.business', 'assets', 'users'])
            ->findOrFail($id);
    }

    public function findById(int $id): ?BranchService
    {
        return BranchService::find($id);
    }

    public function findForBranch(int $branchId, bool $enabledOnly = true): Collection
    {
        $query = BranchService::where('branch_id', $branchId)
            ->with(['service', 'assets', 'users']);

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
     * All instances (including soft-deleted) for a given service template.
     * Used when syncing branch assignments or checking existing coverage.
     */
    public function findForService(int $serviceId): Collection
    {
        return BranchService::withTrashed()
            ->where('service_id', $serviceId)
            ->with(['branch'])
            ->get();
    }

    /**
     * Find a specific instance by service + branch combination.
     * Returns soft-deleted rows too so callers can restore instead of creating a duplicate.
     */
    public function findByServiceAndBranch(int $serviceId, int $branchId): ?BranchService
    {
        return BranchService::withTrashed()
            ->where('service_id', $serviceId)
            ->where('branch_id', $branchId)
            ->first();
    }

    // ── DATA PERSISTENCE ─────────────────────────────────────────

    /**
     * Create a branch service instance from a template (service).
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

    /**
     * Disable and soft-delete the instance.
     * Hard FK references (e.g. appointments) remain intact via soft delete.
     */
    public function deleteInstance(BranchService $branchService): void
    {
        $branchService->update(['is_enabled' => false]);
        $branchService->delete();
    }

    // ── RBAC ─────────────────────────────────────────────────────

    /**
     * Assign a user directly to this branch service instance.
     * Typically used to grant a staff member access to a specific service
     * without giving them branch-wide or business-wide access.
     */
    public function attachUser(BranchService $branchService, int $userId, BranchRoleEnum $role): void
    {
        $branchService->users()->attach($userId, ['role' => $role->value]);
    }

    public function detachUser(BranchService $branchService, int $userId): int
    {
        return $branchService->users()->detach($userId);
    }

    public function getAssignments(BranchService $branchService): array
    {
        return [
            'assets' => $branchService->assets()->pluck('id')->all(),
            'users'  => $branchService->users()->pluck('id')->all(),
        ];
    }

    // ── PRIVATE HELPERS ──────────────────────────────────────────

    private function applyFilters(Builder $query, SearchDTO $dto): void
    {
        if ($dto->query) {
            $keyword = $dto->query;
            $query->where(function ($sub) use ($keyword) {
                $sub->whereHas(
                    'service',
                    fn($s) => $s->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                )
                ->orWhereHas(
                    'branch',
                    fn($b) => $b->where('name', 'like', "%{$keyword}%")
                        ->orWhere('city', 'like', "%{$keyword}%")
                        ->orWhereHas('business', fn($bus) => $bus->where('name', 'like', "%{$keyword}%"))
                );
            });
        }

        if ($dto->city) {
            $query->whereHas('branch', fn($q) => $q->where('city', $dto->city));
        }

        if ($dto->businessId) {
            $query->whereHas('branch', fn($q) => $q->where('business_id', $dto->businessId));
        }

        // Effective price: prefer custom_price, fall back to service base_price
        if ($dto->maxPrice) {
            $query->where(function ($q) use ($dto) {
                $q->where(fn($q) => $q->whereNotNull('custom_price')->where('custom_price', '<=', $dto->maxPrice))
                  ->orWhere(
                      fn($q) => $q->whereNull('custom_price')
                          ->whereHas('service', fn($s) => $s->where('base_price', '<=', $dto->maxPrice))
                  );
            });
        }

        // Effective duration: prefer custom_duration_minutes, fall back to service base
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
