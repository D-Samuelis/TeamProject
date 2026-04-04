<?php

namespace App\Http\Controllers\Web\Business;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

// Requests
use App\Http\Requests\Business\StoreBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;

// DTOs
use App\Application\Business\DTO\StoreBusinessDTO;
use App\Application\Business\DTO\UpdateBusinessDTO;

// Use Cases
use App\Application\Business\UseCases\StoreBusiness;
use App\Application\Business\UseCases\DeleteBusiness;
use App\Application\Business\UseCases\GetBusiness;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Business\UseCases\RestoreBusiness;
use App\Application\Business\UseCases\UpdateBusiness;

class ManageBusinessController extends Controller
{
    // ── DATA PERSISTENCE ─────────────────────────────────────────

    public function store(StoreBusinessRequest $request, StoreBusiness $useCase)
    {
        $business = $useCase->execute(StoreBusinessDTO::fromRequest($request), Auth::user());
        return back()->with('success', "Business '{$business->name}' created successfully.");
    }

    public function update(int $businessId, UpdateBusinessRequest $request, UpdateBusiness $useCase)
    {
        $useCase->execute(UpdateBusinessDTO::fromRequest($businessId, $request), Auth::user());
        return back()->with('success', 'Business updated successfully!');
    }

    public function delete(int $businessId, DeleteBusiness $useCase)
    {
        $useCase->execute($businessId, Auth::user());
        return back()->with('success', 'Business deleted successfully.');
    }

    public function restore(int $businessId, RestoreBusiness $useCase)
    {
        $useCase->execute($businessId, Auth::user());
        return back()->with('success', 'Business restored successfully.');
    }

    // ── VIEWS ─────────────────────────────────────────

    public function index(ListBusinesses $useCase)
    {
        $user = Auth::user();

        return view('pages.business.index', [
            'activeBusinesses' => $useCase->execute($user, 'active'),
            'deletedBusinesses' => $useCase->execute($user, 'deleted'),
        ]);
    }

    public function show(int $businessId, GetBusiness $useCase)
    {
        $business = $useCase->execute($businessId, Auth::user());
        return view('pages.business.show', compact('business'));
    }

    public function book(int $businessId, GetBusiness $useCase)
    {
        $business = $useCase->execute($businessId);
        return view('pages.public.business.book', compact('business'));
    }
}
