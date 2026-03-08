<?php

namespace App\Repositories\Business;

use Illuminate\Support\Collection;
use App\Models\Auth\User;
use App\Models\Business\Business;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;

class BusinessRepository implements BusinessRepositoryInterface
{
    protected array $defaultRelations = ['branches', 'services.branches'];

    public function findById(int $id, bool $withTrashed = false): Business
    {
        $query = Business::with([
            'branches' => function ($query) {
                $query->withTrashed();
            },
            'services' => function ($query) {
                $query->withTrashed();
            },
        ]);

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    public function findDeletedById(int $id): Business
    {
        return Business::onlyTrashed()->with($this->defaultRelations)->findOrFail($id);
    }

    public function listForUser(User $user, string $scope = 'active', bool $loadRelations = false): Collection
    {
        $query = Business::query();
        if ($loadRelations) {
            $query->with($this->defaultRelations);
        }

        if (!$user->isAdmin()) {
            $query->whereHas('users', fn($q) => $q->where('user_id', $user->id));
        }

        match ($scope) {
            'deleted' => $query->onlyTrashed(),
            'all' => $query->withTrashed(),
            default => $query,
        };

        return $query->latest()->get();
    }

    public function save(array $data): Business
    {
        return Business::create($data);
    }

    public function update(int $id, array $data): Business
    {
        $business = $this->findById($id, true);
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

    public function existsOwner(int $userId): bool
    {
        return Business::whereHas('users', function ($q) use ($userId) {
            $q->where('user_id', $userId)->wherePivot('role', BusinessRoleEnum::OWNER->value);
        })->exists();
    }

    public function attachUser(Business $business, int $userId, BusinessRoleEnum $role): void
    {
        $business->users()->attach($userId, ['role' => $role->value]);
    }
}
