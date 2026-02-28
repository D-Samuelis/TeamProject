<?php

namespace App\Infrastructure\Business\Repositories;

use App\Domain\Business\Repositories\ServiceRepositoryInterface;
use App\Models\Business\Service as EloquentService;
use App\Domain\Business\Entities\Service as DomainService;

class EloquentServiceRepository implements ServiceRepositoryInterface
{
    public function findById(int $id): ?DomainService
    {
        $service = EloquentService::find($id);
        return $service ? $this->mapToDomain($service) : null;
    }

    public function findByBusinessId(int $businessId): array
    {
        $services = EloquentService::where('business_id', $businessId)->get();
        return $services->map(fn($s) => $this->mapToDomain($s))->all();
    }

    public function create(array $data): DomainService
    {
        $service = EloquentService::create($data);
        return $this->mapToDomain($service);
    }

    public function attachBranches(DomainService $service, array $branchIds): void
    {
        $eloquentService = EloquentService::findOrFail($service->id);
        $eloquentService->branches()->sync($branchIds);
    }

    public function attachUsers(DomainService $service, array $userIdsWithRoles): void
    {
        $eloquentService = EloquentService::findOrFail($service->id);
        $eloquentService->users()->sync($userIdsWithRoles);
    }

    private function mapToDomain(EloquentService $service): DomainService
    {
        return new DomainService(
            id: $service->id,
            businessId: $service->business_id,
            name: $service->name,
            description: $service->description,
            durationMinutes: $service->duration_minutes,
            price: $service->price,
            locationType: $service->location_type,
            isActive: $service->is_active,
        );
    }
}