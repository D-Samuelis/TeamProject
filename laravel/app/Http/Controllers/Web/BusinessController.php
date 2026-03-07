<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\StoreBusinessRequest;
use App\Application\Business\DTO\CreateBusinessDTO;
use App\Application\Business\DTO\UpdateBusinessDTO;
use App\Application\Business\UseCases\CreateBusiness;
use App\Application\Business\UseCases\DeleteBusiness;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Business\UseCases\RestoreBusiness;

use App\Application\Business\UseCases\UpdateBusiness;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Http\Requests\Business\UpdateBusinessRequest;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index(ListBusinesses $useCase)
    {
        return view('archive.test-admin', [
            'businesses' => $useCase->execute('active'),
            'deletedBusinesses' => $useCase->execute('deleted')
        ]);
    }

    public function store(
        StoreBusinessRequest $request,
        CreateBusiness $useCase
    ) {
        $dto = new CreateBusinessDTO(
            $request->validated('name'),
            $request->validated('description'),
        );

        $business = $useCase->execute($dto, Auth::id());

        return back()->with('success', "Branch '{$business->name}' created successfully.");
    }

    public function update(int $businessId, UpdateBusinessRequest $request, UpdateBusiness $updateBusinessUseCase)
    {
        $dto = new UpdateBusinessDTO(
            $businessId,
            $request->validated('name'),
            $request->validated('description'),
            $request->validated('is_published'),
        );

        $updateBusinessUseCase->execute($dto, Auth::id());

        return back()->with('success', 'Business updated successfully!');
    }

    public function delete(
        int $businessId,
        BusinessRepositoryInterface $businessRepo,
        DeleteBusiness $useCase
    ) {
        $business = $businessRepo->findById($businessId);
        abort_if(!$business, 404);

        $this->authorize('destroy', $business);

        $useCase->execute($businessId, Auth::id());

        return back()->with('success', "Business '{$business->name}' (soft) deleted successfully.");
    }

    public function restore(
        int $businessId,
        BusinessRepositoryInterface $businessRepo,
        RestoreBusiness $useCase
    ) {
        $business = $businessRepo->findDeletedById($businessId);
        abort_if(!$business, 404);

        $useCase->execute($business, Auth::id());

        return back()->with('success', "Business '{$business->name}' restored successfully.");
    }
}
