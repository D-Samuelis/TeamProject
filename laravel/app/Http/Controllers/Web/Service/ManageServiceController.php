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
use App\Models\Auth\User;
use App\Models\Business\Category;
use App\Models\Business\Service;
use App\Notifications\CategoryRequestedNotification;
use Illuminate\Support\Facades\Request;

class ManageServiceController extends Controller
{
    public function index(ListServices $listServices, ListBusinesses $listBusinesses, ListBranches $listBranches)
    {
        return view('web.manage.service.index', [
            'services'   => $listServices->execute(Auth::user(), scope: 'all'),
            'services'   => $listServices->execute(Auth::user(), scope: 'all'),
            'businesses' => $listBusinesses->execute(Auth::user()),
            'branches' => $listBranches->execute(Auth::user()),
            'categories' => Category::orderBy('name', 'asc')->get(),
        ]);
    }

    public function show(int $serviceId, GetService $getService, ListBusinesses $listBusinesses, ListBranches $listBranches)
    {
        $service = $getService->execute($serviceId, Auth::user());
        return view('web.manage.service.show', [
            'service'    => $service,
            'service'    => $service,
            'businesses' => $listBusinesses->execute(Auth::user()),
            'branches' => $listBranches->execute(Auth::user()),
            'categories' => Category::orderBy('name', 'asc')->get(),
        ]);
    }

    public function store(StoreServiceRequest $request, StoreService $useCase)
    {
        $service = $useCase->execute(StoreServiceDTO::fromRequest($request), Auth::user());
        return response()->json(['message' => "Service '{$service->name}' created successfully.", 'data' => $service], 201);
        return response()->json(['message' => "Service '{$service->name}' created successfully.", 'data' => $service], 201);
    }

    public function update(int $serviceId, UpdateServiceRequest $request, UpdateService $useCase)
    {
        $service = $useCase->execute(UpdateServiceDTO::fromRequest($serviceId, $request), Auth::user());
        return response()->json(['message' => "Service '{$service->name}' updated successfully.", 'data' => $service]);
    }

    public function requestCategory(Request $request)
    {
        $validated = $request->validate([
            'requested_category_name' => ['required', 'string', 'max:100'],
            'service_name' => ['nullable', 'string', 'max:255'],
            'business_id' => ['nullable', 'integer', 'exists:businesses,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
        ]);

        $service = isset($validated['service_id'])
            ? Service::find($validated['service_id'], ['id', 'name'])
            : null;

        User::where('role', 'is', 'admin', true)
            ->get()
            ->each(fn(User $admin) => $admin->notify(new CategoryRequestedNotification(
                Auth::user(),
                trim($validated['requested_category_name']),
                $service,
                $validated['service_name'] ?? null,
                $validated['business_id'] ?? null
            )));

        return back()->with('success', 'Category request was sent to admin.');
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