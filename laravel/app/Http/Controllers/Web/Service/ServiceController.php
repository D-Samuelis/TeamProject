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
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index(ListServices $useCase)
    {
        return view('pages.private.service.index', [
            'services' => $useCase->execute(),
        ]);
    }

    public function store(StoreServiceRequest $request, StoreService $useCase)
    {
        $useCase->execute(StoreServiceDTO::fromRequest($request), Auth::id());
        return back()->with('success', 'Service created successfully.');
    }

    public function show(int $serviceId, GetService $useCase)
    {
        $service = $useCase->execute($serviceId, Auth::user());
        return view('pages.private.service.show', compact('service'));
    }

    public function update(
        int $serviceId,
        UpdateServiceRequest $request,
        UpdateService $useCase
    ) {
        $useCase->execute(UpdateServiceDTO::fromRequest($serviceId, $request), Auth::id());
        return back()->with('success', 'Service updated successfully!');
    }

    public function delete(
        int $serviceId,
        DeleteService $useCase
    ) {
        $useCase->execute($serviceId, Auth::id());
        return back()->with('success', 'Service moved to trash.');
    }

    public function restore(
        int $serviceId,
        RestoreService $useCase
    ) {
        $useCase->execute($serviceId, Auth::id());
        return back()->with('success', 'Service restored successfully.');
    }
}
