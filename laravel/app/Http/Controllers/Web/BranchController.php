<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Application\Business\DTO\CreateBranchDTO;
use App\Application\Business\UseCases\CreateBranch;
use App\Http\Requests\Business\StoreBranchRequest;

class BranchController extends Controller
{
    public function store(
        StoreBranchRequest $request,
        CreateBranch $useCase
    ) {       
        $dto = new CreateBranchDTO(
            $request->validated('business_id'),
            $request->validated('name'),
            $request->validated('type'),
            $request->validated('address_line_1'),
            $request->validated('address_line_2'),
            $request->validated('city'),
            $request->validated('postal_code'),
            $request->validated('country'),
        );

        $useCase->execute($dto, auth()->id());

        return back();
    }
}
