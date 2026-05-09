<?php

namespace App\Http\Controllers\Web\Business;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Business\StoreBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;

use App\Application\Business\DTO\StoreBusinessDTO;
use App\Application\Business\DTO\UpdateBusinessDTO;

use App\Application\Business\UseCases\StoreBusiness;
use App\Application\Business\UseCases\DeleteBusiness;
use App\Application\Business\UseCases\GetBusiness;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Business\UseCases\RestoreBusiness;
use App\Application\Business\UseCases\UpdateBusiness;

class ManageBusinessController extends Controller
{
    public function index(ListBusinesses $useCase)
    {
        return view('web.manage.business.index', [
            'activeBusinesses'  => $useCase->execute(Auth::user(), 'active'),
            'deletedBusinesses' => $useCase->execute(Auth::user(), 'deleted'),
        ]);
    }

    public function show(int $businessId, GetBusiness $useCase)
    {
        $business = $useCase->execute($businessId, Auth::user());

        if (request()->expectsJson()) {
            return response()->json(['data' => $business]);
        }

        return view('web.manage.business.show', compact('business'));
    }

    public function store(StoreBusinessRequest $request, StoreBusiness $useCase)
    {
        $business = $useCase->execute(StoreBusinessDTO::fromRequest($request), Auth::user());
        return response()->json(['message' => "Business '{$business->name}' created successfully.", 'data' => $business], 201);
    }

    public function update(int $businessId, UpdateBusinessRequest $request, UpdateBusiness $useCase)
    {
        $business = $useCase->execute(UpdateBusinessDTO::fromRequest($businessId, $request), Auth::user());
        return response()->json(['message' => "Business '{$business->name}' updated successfully.", 'data' => $business]);
    }

    public function delete(int $businessId, DeleteBusiness $useCase)
    {
        $useCase->execute($businessId, Auth::user());
        return response()->json(['message' => 'Business deleted successfully.', 'data' => $businessId]);
    }

    public function restore(int $businessId, RestoreBusiness $useCase)
    {
        $business = $useCase->execute($businessId, Auth::user());
        return response()->json(['message' => 'Business restored successfully.', 'data' => $business]);
    }
}
