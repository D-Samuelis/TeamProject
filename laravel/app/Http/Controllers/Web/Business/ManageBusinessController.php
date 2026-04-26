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

// Exceptions
use Illuminate\Auth\Access\AuthorizationException;
use App\Exceptions\Business\BusinessNotFoundException;
use App\Exceptions\Business\BusinessCreationFailedException;

class ManageBusinessController extends Controller
{
    public function index(ListBusinesses $useCase)
    {
        $user = Auth::user();

        try {
            return view('web.manage.business.index', [
                'activeBusinesses'  => $useCase->execute($user, 'active'),
                'deletedBusinesses' => $useCase->execute($user, 'deleted'),
            ]);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('login');
        } catch (\Throwable $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show(int $businessId, GetBusiness $useCase)
    {
        try {
            $business = $useCase->execute($businessId, Auth::user());
            return view('web.manage.business.show', compact('business'));
        } catch (BusinessNotFoundException $e) {
            return redirect()->route('manage.business.index')->with('error', 'Business not found.');
        } catch (AuthorizationException $e) {
            return redirect()->route('manage.business.index')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function store(StoreBusinessRequest $request, StoreBusiness $useCase)
    {
        try {
            $business = $useCase->execute(StoreBusinessDTO::fromRequest($request), Auth::user());
            return back()->with('success', "Business '{$business->name}' created successfully.");
        } catch (AuthorizationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (BusinessCreationFailedException $e) {
            return back()->with('error', 'Something went wrong while creating the business.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function update(int $businessId, UpdateBusinessRequest $request, UpdateBusiness $useCase)
    {
        try {
            $useCase->execute(UpdateBusinessDTO::fromRequest($businessId, $request), Auth::user());
            return back()->with('success', 'Business updated successfully!');
        } catch (BusinessNotFoundException $e) {
            return redirect()->route('manage.business.index')->with('error', 'Business not found.');
        } catch (AuthorizationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function delete(int $businessId, DeleteBusiness $useCase)
    {
        try {
            $useCase->execute($businessId, Auth::user());
            return back()->with('success', 'Business deleted successfully.');
        } catch (BusinessNotFoundException $e) {
            return back()->with('error', 'Business not found.');
        } catch (AuthorizationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function restore(int $businessId, RestoreBusiness $useCase)
    {
        try {
            $useCase->execute($businessId, Auth::user());
            return back()->with('success', 'Business restored successfully.');
        } catch (BusinessNotFoundException $e) {
            return back()->with('error', 'Business not found.');
        } catch (AuthorizationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
