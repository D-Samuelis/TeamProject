<?php

namespace App\Repositories\Business;

use App\Application\DTO\SearchDTO;
use Illuminate\Support\Collection;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

class BusinessRepository implements BusinessRepositoryInterface
{
    // ── DATA PERSISTENCE ─────────────────────────────────────────

    public function save(array $data): Business
    {
        return Business::create($data);
    }

    public function update(int $id, array $data): Business
    {
        $business = Business::withTrashed()->findOrFail($id);
        $business->update($data);
        return $business;
    }

    public function delete(Business $business): void
    {
        $business->update([
            'delete_after' => now()->addDays(7),
            'is_published' => false,
        ]);
        $business->delete();
    }

    public function restore(Business $business): void
    {
        $business->update(['delete_after' => null]);
        $business->restore();
    }

    // ── RBAC ─────────────────────────────────────────────────────

    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void
    {
        $business->users()->attach($userId, ['role' => $role->value]);
    }

    public function detachUser(Business $business, int $userId): int
    {
        return $business->users()->detach($userId);
    }

    // ── PUBLIC ───────────────────────────────────────────────────

    public function findActive(int $id): Business
    {
        return Business::published()
            ->with([
                'branches' => fn($q) =>
                $q->where('is_active', true)
                    ->with([
                        'branchServices' => fn($q) => $q->withTrashed(),
                        'branchServices.service' => fn($q) => $q->withTrashed(),
                    ])
            ])
            ->findOrFail($id);
    }

    public function search(SearchDTO $dto)
    {
        return Business::published()
            ->search($dto)
            ->with([
                'branches' => fn($q) => $q->where('is_active', true),
                'services' => fn($q) => $q->where('is_active', true),
            ])
            ->latest()
            ->paginate($dto->perPage);
    }

    public function count(SearchDTO $dto): int
    {
        return Business::published()->search($dto)->count();
    }

    // ── MANAGEMENT ───────────────────────────────────────────────

    public function listForUser(User $user, string $scope = 'active'): Collection
    {
        return Business::trashedScope($scope)
            ->forUser($user)
            ->with(['branches', 'services'])
            ->latest()
            ->get();
    }

    public function findForManagement(int $id): Business
    {
        return Business::withTrashed()
            ->with([
                'users',
                'branches' => fn($q) => $q->withTrashed(),
                'branches.users',
                'branches.branchServices',
                'branches.branchServices.users',
                'branches.branchServices.service',
            ])
            ->findOrFail($id);
    }
}
