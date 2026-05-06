<?php

namespace App\Http\Controllers\Web\Service;

use App\Http\Controllers\Controller;
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
use App\Application\Branch\UseCases\ListBranches;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Service\UseCases\AssignServiceToBranch;
use App\Application\Service\UseCases\UnassignServiceFromBranch;
use Illuminate\Support\Facades\Auth;

class ManageServiceController extends Controller
{
    public function index(ListServices $listServices, ListBusinesses $listBusinesses, ListBranches $listBranches)
    {
        return view('web.manage.service.index', [
            'services' => $listServices->execute(Auth::user(), scope: 'all'),
            'businesses' => $listBusinesses->execute(Auth::user()),
            'branches' => $listBranches->execute(Auth::user()),
        ]);
    }

    public function show(int $serviceId, GetService $getService, ListBusinesses $listBusinesses, ListBranches $listBranches)
    {
        $service = $getService->execute($serviceId, Auth::user());
        return view('web.manage.service.show', [
            'service' => $service,
            'businesses' => $listBusinesses->execute(Auth::user()),
            'branches' => $listBranches->execute(Auth::user()),
        ]);
    }

    public function store(StoreServiceRequest $request, StoreService $useCase)
    {
        $service = $useCase->execute(StoreServiceDTO::fromRequest($request), Auth::user());
        return back()->with('success', "Service '{$service->name}' created successfully.");
    }

    public function update(int $serviceId, UpdateServiceRequest $request, UpdateService $useCase)
    {
        $service = $useCase->execute(UpdateServiceDTO::fromRequest($serviceId, $request), Auth::user());
        return back()->with('success', "Service '{$service->name}' updated successfully.");
    }

    public function delete(int $serviceId, DeleteService $useCase)
    {
        $useCase->execute($serviceId, Auth::user());
        return back()->with('success', 'Service moved to trash.');
    }

    public function restore(int $serviceId, RestoreService $useCase)
    {
        $useCase->execute($serviceId, Auth::user());
        return back()->with('success', 'Service restored successfully.');
    }

    public function assign(int $serviceId, int $branchId, AssignServiceToBranch $useCase)
    {
        try {
            $useCase->execute($serviceId, $branchId, Auth::user());
        } catch (\DomainException $exception) {
            return redirect()->route('manage.service.show', $serviceId)->with('error', $exception->getMessage());
        }
        return redirect()->route('manage.service.show', $serviceId)->with('success', 'Service assigned to branch.');
    }

    public function unassign(int $serviceId, int $branchId, UnassignServiceFromBranch $useCase)
    {
        try {
            $useCase->execute($serviceId, $branchId, Auth::user());
            
            // Ak je to AJAX (z tvojho JS)
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Service removed from branch.'
                ]);
            }

            return redirect()->route('manage.service.show', $serviceId)
                ->with('success', 'Service removed from branch.');

        } catch (\Exception $exception) {
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $exception->getMessage()
                ], 422);
            }

            return redirect()->route('manage.service.show', $serviceId)
                ->with('error', $exception->getMessage());
        }
    }
    
    public function book(int $serviceId, GetService $useCase)
    {
        $service = $useCase->execute($serviceId);
        return view('book.service.book', compact('service'));
    }
}
