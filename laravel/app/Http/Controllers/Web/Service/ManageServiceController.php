<?php

namespace App\Http\Controllers\Web\Service;

use App\Application\DTO\BranchSearchDTO;
use App\Application\DTO\BusinessSearchDTO;
use App\Application\DTO\ServiceSearchDTO;
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
use App\Models\Auth\User;
use App\Models\Business\Category;
use App\Models\Business\Business;
use App\Models\Business\Service;
use App\Notifications\CategoryRequestedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageServiceController extends Controller
{
    public function index(Request $request, ListServices $listServices, ListBusinesses $listBusinesses, ListBranches $listBranches)
    {
        $user = Auth::user();
        $dto = ServiceSearchDTO::fromArray($request->query());
        $paginator = $listServices->execute($dto, $user);

        return view('web.manage.service.index', [
            'services' => $paginator->getCollection(),
            'businesses' => $listBusinesses->execute(BusinessSearchDTO::fromArray([]), Auth::user())->getCollection(),
            'branches' => $listBranches->execute(BranchSearchDTO::fromArray([]), Auth::user())->getCollection(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
            'selectedUser'     => $request->user_id ? User::find((int) $request->user_id, ['id', 'name', 'email']) : null,
            'selectedBusiness' => $request->business_id ? Business::find((int) $request->business_id, ['id', 'name']) : null,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(int $serviceId, GetService $getService, ListBusinesses $listBusinesses, ListBranches $listBranches)
    {
        $service = $getService->execute($serviceId, Auth::user());
        return view('web.manage.service.show', [
            'service' => $service,
            'businesses' => $listBusinesses->execute(BusinessSearchDTO::fromArray([]), Auth::user())->getCollection(),
            'branches' => $listBranches->execute(BranchSearchDTO::fromArray([]), Auth::user())->getCollection(),
            'categories' => Category::orderBy('name')->get(),
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

    public function requestCategory(Request $request)
    {
        $validated = $request->validate([
            'requested_category_name' => ['required', 'string', 'max:100'],
            'service_name' => ['nullable', 'string', 'max:255'],
            'business_id' => ['nullable', 'integer', 'exists:businesses,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
        ]);

        $service = isset($validated['service_id'])
            ? Service::find($validated['service_id'])
            : null;

        User::where('is_admin', true)
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

    public function requestCategory(Request $request)
    {
        $validated = $request->validate([
            'requested_category_name' => ['required', 'string', 'max:100'],
            'service_name' => ['nullable', 'string', 'max:255'],
            'business_id' => ['nullable', 'integer', 'exists:businesses,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
        ]);

        $service = isset($validated['service_id'])
            ? Service::find($validated['service_id'])
            : null;

        User::where('is_admin', true)
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

    public function search(Request $request, ListServices $listServices): JsonResponse
    {
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $dto = ServiceSearchDTO::fromArray([
            'service_name' => $query,
            'per_page'     => 8,
        ]);

        $results = $listServices->execute($dto, Auth::user())
            ->getCollection()
            ->map(fn($s) => ['id' => $s->id, 'name' => $s->name]);

        return response()->json($results);
    }
}