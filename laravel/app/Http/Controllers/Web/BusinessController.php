<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Http\Requests\Business\StoreBusinessRequest;

use App\Application\Business\DTO\CreateBusinessDTO;
use App\Application\Business\UseCases\CreateBusiness;
use App\Application\Business\UseCases\ListBusinesses;

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
}
