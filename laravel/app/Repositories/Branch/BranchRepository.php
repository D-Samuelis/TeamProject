<?php

namespace App\Repositories\Branch;

use App\Application\DTO\SearchDTO;
use App\Domain\Branch\Enums\BranchRoleEnum;
use Illuminate\Support\Collection;
use App\Models\Business\Branch;
use App\Models\Business\BranchService;
use App\Models\Auth\User;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;

class BranchRepository implements BranchRepositoryInterface
{
    // ── DATA PERSISTENCE ─────────────────────────────────────────

    public function save(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(Branch $branch, array $data): Branch
    {
        $branch->update($data);
        return $branch;
    }

    public function delete(Branch $branch): void
    {
        $branch->update([
            'delete_after' => now()->addDays(7),
            'is_active'    => false,
        ]);

        // Disable all services immediately
        $branch->branchServices()->update(['is_enabled' => false]);

        $branch->delete();
    }

    public function restore(Branch $branch): void
    {
        if ($branch->trashed()) {
            $branch->restore();
        }
        $branch->update(['delete_after' => null]);
    }

    // ── RBAC ─────────────────────────────────────────────────────

    public function attachUser(Branch $branch, int $userId, BranchRoleEnum $role): void
    {
        $branch->users()->syncWithoutDetaching([$userId => ['role' => $role->value]]);
    }

    public function detachUser(Branch $branch, int $userId): int
    {
        return $branch->users()->detach($userId);
    }

    public function getAssignments(Branch $branch): array
    {
        return [
            'branch_services' => $branch->branchServices()->pluck('id')->all(),
            'users'           => $branch->users()->pluck('id')->all(),
        ];
    }

    public function attachServices(Branch $branch, array $serviceIds): void
    {
        foreach ($serviceIds as $serviceId) {
            BranchService::firstOrCreate(
                ['branch_id' => $branch->id, 'service_id' => $serviceId],
                ['is_enabled' => true]
            );
        }
    }

    // ── PUBLIC METHODS ──────────────────────────────────────────

    public function findActive(int $id): Branch
    {
        return Branch::active()
            ->publishedBusiness()
            ->findOrFail($id);
    }

    public function search(SearchDTO $dto)
    {
        $query = Branch::active()
            ->publishedBusiness()
            ->search($dto);

        return $query->with('business')
            ->latest()
            ->paginate($dto->perPage);
    }

    public function count(SearchDTO $dto): int
    {
        $query = Branch::active()
            ->publishedBusiness()
            ->search($dto);

        return $query->count();
    }

    public function findMultipleByIds(array $ids): Collection
    {
        return Branch::whereIn('id', $ids)->get();
    }

    // ── MANAGEMENT METHODS ──────────────────────────────────────

    public function listForUser(User $user, ?int $businessId = null, string $scope = 'active'): Collection
    {
        $query = Branch::query()
            ->forUser($user);

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $query->trashedScope($scope);

        return $query->with(['business', 'branchServices.service', 'assets'])
            ->latest()
            ->get();
    }

    public function findForManagement(int $id): Branch
    {
        return Branch::withTrashed()
            ->with(['business', 'branchServices.service', 'assets'])
            ->findOrFail($id);
    }

    public function findByBusinessId(int $businessId, string $scope = 'active'): Collection
    {
        return Branch::where('business_id', $businessId)
            ->trashedScope($scope)
            ->get();
    }

    public function findWithinBusiness(int $branchId, int $businessId): Branch
    {
        return Branch::where('id', $branchId)
            ->where('business_id', $businessId)
            ->firstOrFail();
    }
}
