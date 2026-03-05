<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Http\Requests\Business\StoreBusinessRequest;

use App\Application\Business\DTO\CreateBusinessDTO;
use App\Application\Business\UseCases\CreateBusiness;
use App\Application\Business\UseCases\DeleteBusiness;
use App\Application\Business\UseCases\ListBusinesses;
use App\Domain\Business\Repositories\BusinessRepositoryInterface;

class BusinessController extends Controller
{
    public function index(ListBusinesses $useCase)
    {
        return view('archive.test-admin', [
            'businesses' => $useCase->execute()
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

        $useCase->execute($dto, auth()->id());

        return back();
    }

    public function destroy(
        int $businessId,
        BusinessRepositoryInterface $businessRepo,
        DeleteBusiness $useCase
    ) {
        $business = $businessRepo->findById($businessId);

        abort_if(!$business, 404);

        $this->authorize('delete', $business);

        $useCase->execute($business, auth()->id());

        return back();
    }
}
