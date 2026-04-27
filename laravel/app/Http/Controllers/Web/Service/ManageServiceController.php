<?php

namespace App\Http\Controllers\Web\Service;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;

use App\Application\Service\DTO\StoreServiceDTO;
use App\Application\Service\DTO\UpdateServiceDTO;

use App\Application\Service\UseCases\StoreService;
use App\Application\Service\UseCases\UpdateService;
use App\Application\Service\UseCases\DeleteService;
use App\Application\Service\UseCases\RestoreService;
use App\Application\Service\UseCases\GetService;
use App\Application\Service\UseCases\ListServices;
use App\Application\Service\UseCases\AssignServiceToBranch;
use App\Application\Service\UseCases\UnassignServiceFromBranch;
use App\Application\Branch\UseCases\ListBranches;
use App\Application\Business\UseCases\ListBusinesses;

class ManageServiceController extends Controller
{
    public function index(ListServices $listServices, ListBusinesses $listBusinesses, ListBranches $listBranches)
    {
        return view('web.manage.service.index', [
            'services'   => $listServices->execute(Auth::user(), scope: 'all'),
            'businesses' => $listBusinesses->execute(Auth::user()),
            'branches'   => $listBranches->execute(Auth::user()),
        ]);
    }

    public function show(int $serviceId, GetService $getService, ListBusinesses $listBusinesses, ListBranches $listBranches)
    {
        $service = $getService->execute($serviceId, Auth::user());
        return view('web.manage.service.show', [
            'service'    => $service,
            'businesses' => $listBusinesses->execute(Auth::user()),
            'branches'   => $listBranches->execute(Auth::user()),
        ]);
    }

    public function store(StoreServiceRequest $request, StoreService $useCase)
    {
        $service = $useCase->execute(StoreServiceDTO::fromRequest($request), Auth::user());
        return response()->json(['message' => "Service '{$service->name}' created successfully.", 'data' => $service], 201);
    }

    public function update(int $serviceId, UpdateServiceRequest $request, UpdateService $useCase)
    {
        $service = $useCase->execute(UpdateServiceDTO::fromRequest($serviceId, $request), Auth::user());
        return response()->json(['message' => "Service '{$service->name}' updated successfully.", 'data' => $service]);
    }

    public function delete(int $serviceId, DeleteService $useCase)
    {
        $useCase->execute($serviceId, Auth::user());
        return response()->json(['message' => 'Service deleted successfully.', 'data' => $serviceId]);
    }

    public function restore(int $serviceId, RestoreService $useCase)
    {
        $service = $useCase->execute($serviceId, Auth::user());
        return response()->json(['message' => 'Service restored successfully.', 'data' => $service]);
    }

    public function assign(int $serviceId, int $branchId, AssignServiceToBranch $useCase)
    {
        $useCase->execute($serviceId, $branchId, Auth::user());
        return response()->json(['message' => 'Service assigned to branch successfully.', 'data' => $serviceId]);
    }

    public function unassign(int $serviceId, int $branchId, UnassignServiceFromBranch $useCase)
    {
        $useCase->execute($serviceId, $branchId, Auth::user());
        return response()->json(['message' => 'Service removed from branch successfully.', 'data' => $serviceId]);
    }
}