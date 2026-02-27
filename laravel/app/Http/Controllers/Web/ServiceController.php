<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Application\Business\UseCases\CreateService;

use App\Http\Requests\Business\StoreServiceRequest;


class ServiceController extends Controller
{
    public function store(
        StoreServiceRequest $request,
        CreateService $useCase
    ) {
        $useCase->execute($request->validated(), auth()->id());

        return back();
    }
}
