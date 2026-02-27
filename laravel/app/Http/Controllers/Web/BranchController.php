<?php

namespace App\Http\Controllers;

use App\Application\Business\UseCases\CreateBranch;
use App\Http\Requests\Business\StoreBranchRequest;

class BranchController extends Controller
{
    public function store(
        StoreBranchRequest $request,
        CreateBranch $useCase
    ) {
        $useCase->execute($request->validated(), auth()->id());

        return back();
    }
}
