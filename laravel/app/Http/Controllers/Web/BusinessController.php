<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\StoreBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;
use App\Application\Business\UseCases\CreateBusiness;
use App\Application\Business\UseCases\DeleteBusiness;
use App\Application\Business\UseCases\GetBusiness;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Business\UseCases\RestoreBusiness;
use App\Application\Business\UseCases\UpdateBusiness;
use App\Application\Business\DTO\CreateBusinessDTO;
use App\Application\Business\DTO\UpdateBusinessDTO;

class BusinessController extends Controller
{
    public function index(ListBusinesses $useCase)
    {
        $user = Auth::user();

        return view('pages.business.index', [
            'businesses' => $useCase->execute($user, 'active'),
            'deletedBusinesses' => $useCase->execute($user, 'deleted'),
        ]);
    }

    public function show(int $businessId, GetBusiness $useCase)
    {
        $business = $useCase->execute($businessId, Auth::user());
        return view('pages.business.show', compact('business'));
    }

    public function store(StoreBusinessRequest $request, CreateBusiness $useCase)
    {
        $business = $useCase->execute(CreateBusinessDTO::fromRequest($request), Auth::id());
        return back()->with('success', "Business '{$business->name}' created successfully.");
    }

    public function update(int $businessId, UpdateBusinessRequest $request, UpdateBusiness $useCase)
    {
        $useCase->execute(UpdateBusinessDTO::fromRequest($businessId, $request), Auth::id());
        return back()->with('success', 'Business updated successfully!');
    }

    public function delete(int $businessId, DeleteBusiness $useCase)
    {
        $useCase->execute($businessId, Auth::id());
        return back()->with('success', 'Business deleted successfully.');
    }

    public function restore(int $businessId, RestoreBusiness $useCase)
    {
        $useCase->execute($businessId, Auth::id());
        return back()->with('success', 'Business restored successfully.');
    }
}
