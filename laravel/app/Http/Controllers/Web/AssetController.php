<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Http\Requests\Business\StoreAssetRequest;

use App\Application\Business\DTO\CreateAssetDTO;
use App\Application\Business\UseCases\CreateAsset;

class AssetController extends Controller
{
    public function store(StoreAssetRequest $request, CreateAsset $useCase)
    {
        $dto = new CreateAssetDTO(
            $request->validated('branch_id'),
            $request->validated('service_id'),
            $request->validated('name'),
            $request->validated('description'),
        );

        $useCase->execute($dto, auth()->id());

        return back();
    }
}
