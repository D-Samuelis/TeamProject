<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Http\Requests\Business\StoreBusinessRequest;

use App\Application\Business\DTO\CreateBusinessDTO;
use App\Application\Business\UseCases\CreateBusiness;
use App\Application\Business\UseCases\DeleteBusiness;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Business\UseCases\RestoreBusiness;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;
use Illuminate\Support\Facades\Auth;

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
