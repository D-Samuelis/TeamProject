<?php

namespace App\Repositories\Service;

use Illuminate\Support\Collection;
use App\Models\Business\Service;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;

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
        $service->delete();       // requires SoftDeletes trait on Service model
    }

    public function attachBranches(Service $service, array $branchIds): void
    {
        $service->branches()->sync($branchIds);
    }

    public function attachUsers(Service $service, array $userIdsWithRoles): void
    {
        // $userIdsWithRoles = [userId => ['role' => 'staff'], ...]
        $service->users()->sync($userIdsWithRoles);
    }
}