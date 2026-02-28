<?php

namespace App\Infrastructure\Business\Repositories;

use Illuminate\Support\Collection;

use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use App\Domain\Business\Entities\Business as DomainBusiness;

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

    public function create(array $data): DomainBusiness
    {
        $business = EloquentBusiness::create($data);
        return $this->mapToDomain($business);
    }

    public function update(DomainBusiness $business, array $data): DomainBusiness
    {
        $eloquent = EloquentBusiness::findOrFail($business->id);
        $eloquent->update($data);
        return $this->mapToDomain($eloquent);
    }

    public function existsOwner(int $userId): bool
    {
        return EloquentBusiness::whereHas('users', fn($q) => $q->where('user_id', $userId)->wherePivot('role', 'owner'))->exists();
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
}
