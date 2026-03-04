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

    public function findByBusinessId(int $business_id): array
    {
        $services = EloquentService::where('business_id', $business_id)->get();
        return $services->map(fn($s) => $this->mapToDomain($s))->all();
    }

    public function save(DomainService $data): DomainService
    {
        $service = EloquentService::create([
            'business_id' => $data->business_id,
            'name' => $data->name,
            'description' => $data->description,
            'duration_minutes' => $data->duration_minutes,
            'price' => $data->price,
            'location_type' => $data->location_type,
            'is_active' => $data->is_active,
        ]);
        return $this->mapToDomain($service);
    }

    public function attachBranches(DomainService $service, array $branch_ids): void
    {
        $eloquentService = EloquentService::findOrFail($service->id);
        $eloquentService->branches()->sync($branch_ids);
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
            business_id: $service->business_id,
            name: $service->name,
            description: $service->description,
            duration_minutes: $service->duration_minutes,
            price: $service->price,
            location_type: $service->location_type,
            is_active: $service->is_active,
        );
    }
}