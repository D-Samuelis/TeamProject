<?php

namespace App\Infrastructure\Business\Repositories;

use Illuminate\Support\Collection;

use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Domain\Business\Entities\Business as DomainBusiness;
use App\Domain\Business\Enums\BusinessRoleEnum;
use App\Domain\Business\Enums\BusinessStateEnum;
use App\Models\Business\Business as EloquentBusiness;

class EloquentBusinessRepository implements BusinessRepositoryInterface
{
    public function findById(int $id): ?DomainBusiness
    {
        $business = EloquentBusiness::find($id);
        return $business ? $this->mapToDomain($business) : null;
    }

    public function findByUserId(int $userId): array
    {
        $businesses = EloquentBusiness::whereHas('users', fn($q) => $q->where('user_id', $userId))->get();
        return $businesses->map(fn($b) => $this->mapToDomain($b))->all();
    }

    public function save(DomainBusiness $business): DomainBusiness
    {
        $eloquent = EloquentBusiness::create([
            'name' => $business->name,
            'description' => $business->description,
            'state' => $business->state,
            'is_published' => $business->isPublished,
        ]);

        return $this->mapToDomain($eloquent);
    }

    public function update(DomainBusiness $business, array $data): DomainBusiness
    {
        $eloquent = EloquentBusiness::findOrFail($business->id);
        $eloquent->update($data);
        return $this->mapToDomain($eloquent);
    }

    public function delete(int $businessId, int $userId): void
    {
        $business = EloquentBusiness::findOrFail($businessId);

        $business->update([
            'state' => BusinessStateEnum::DELETED,
            'delete_after' => now()->addDays(7)
        ]);

        $business->delete();
    }

    public function existsOwner(int $userId): bool
    {
        return EloquentBusiness::whereHas('users', fn($q) => $q->where('user_id', $userId)->wherePivot('role', BusinessRoleEnum::OWNER->value))->exists();
    }

    public function getOwners(int $businessId): array
    {
        $business = EloquentBusiness::find($businessId);
        if (!$business) {
            return [];
        }
        return $business->users()->wherePivot('role', 'owner')->pluck('user_id')->all();
    }

    private function mapToDomain(EloquentBusiness $business): DomainBusiness
    {
        return new DomainBusiness(
            id: $business->id,
            name: $business->name,
            description: $business->description,
            state: $business->state,
            isPublished: $business->is_published,
        );
    }

    public function allWithRelations(): Collection
    {
        return EloquentBusiness::with([
            'branches',
            'services.branches'
        ])->get();
    }

    public function attachUser(int $businessId, int $userId, BusinessRoleEnum $role): void
    {
        $business = EloquentBusiness::findOrFail($businessId);
        $business->users()->attach($userId, ['role' => $role->value]);
    }
}
