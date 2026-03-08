<?php

namespace App\Repositories\Service;

use Illuminate\Support\Collection;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Business\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function findById(int $id): ?Service
    {
        return Service::find($id);
    }

    public function findByBusinessId(int $businessId): Collection
    {
        return Service::where('business_id', $businessId)->get();
    }

    public function save(array $data): Service
    {
        return Service::create($data);
    }

    public function delete(Service $service): void
    {
        $service->delete();
    }

    public function attachBranches(Service $service, array $branchIds): void
    {
        $service->branches()->sync($branchIds);
    }

    public function attachUsers(Service $service, array $userIdsWithRoles): void
    {
        $service->users()->sync($userIdsWithRoles);
    }
}
