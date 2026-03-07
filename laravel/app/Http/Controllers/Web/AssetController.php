<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\StoreAssetRequest;
use App\Http\Requests\Asset\UpdateAssetRequest;
use App\Application\Asset\DTO\CreateAssetDTO;
use App\Application\Asset\DTO\UpdateAssetDTO;
use App\Application\Asset\UseCases\CreateAsset;
use App\Application\Asset\UseCases\DeleteAsset;
use App\Application\Asset\UseCases\UpdateAsset;

use App\Domain\Asset\Interfaces\AssetRepositoryInterface;

use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function store(StoreAssetRequest $request, CreateAsset $useCase)
    {
        $dto = new CreateAssetDTO(
            $request->validated('name'),
            $request->validated('description'),
            $request->validated('branch_ids') ?? [],
            $request->validated('service_ids') ?? []
        );

        $useCase->execute($dto, Auth::id());

        return back();
    }

    public function update(int $assetId, UpdateAssetRequest $request, UpdateAsset $updateAssetUseCase)
    {
        $dto = new UpdateAssetDTO(
            $assetId,
            $request->validated('name'),
            $request->validated('description'),
            $request->validated('branch_ids') ?? [],
            $request->validated('service_ids') ?? []
        );

        $updateAssetUseCase->execute($dto, Auth::id());

        return back()->with('success', 'Asset updated successfully!');
    }

    public function delete(
        int $assetId,
        AssetRepositoryInterface $AssetRepo,
        DeleteAsset $useCase
    ) {
        $asset = $AssetRepo->findById($assetId);
        abort_if(!$asset, 404);

        $this->authorize('destroy', $asset);

        $useCase->execute($assetId, Auth::id());

        return back()->with('success', "Asset '{$asset->name}' (soft) deleted successfully.");
    }

    public function restore()
    {
        return back();
    }
}
