<?php

namespace App\Http\Controllers\Web\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\StoreAssetRequest;
use App\Http\Requests\Asset\UpdateAssetRequest;
use App\Http\Requests\Asset\GetAssetRequest;
use App\Application\Asset\DTO\CreateAssetDTO;
use App\Application\Asset\DTO\UpdateAssetDTO;
use App\Application\Asset\UseCases\CreateAsset;
use App\Application\Asset\UseCases\DeleteAsset;
use App\Application\Asset\UseCases\UpdateAsset;
use App\Application\Asset\UseCases\GetAsset;
use App\Application\Asset\UseCases\ListAssets;
use App\Application\Branch\UseCases\ListBranches;
use App\Application\Service\UseCases\ListServices;
use App\Application\Service\UseCases\GetService;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index(ListAssets $listAssets, ListBranches $listBranches, ListServices $listServices)
    {
        return view('pages.private.asset.index', [
            'assets'   => $listAssets->execute(),
            'branches' => $listBranches->execute(),
            'services' => $listServices->execute(),
        ]);
    }

    public function store(StoreAssetRequest $request, CreateAsset $useCase)
    {
        $dto = new CreateAssetDTO(
            $request->validated('name'),
            $request->validated('description'),
            $request->validated('branch_ids') ?? [],
            $request->validated('service_ids') ?? []
        );

        $useCase->execute($dto, Auth::id());

        return back()->with('success', 'Asset created successfully.');
    }

    public function show(int $assetId, GetAsset $getAsset, ListBranches $listBranches, ListServices $listServices)
    {
        $asset = $getAsset->execute($assetId, Auth::user());
        $asset->load('branches', 'services');

        return view('pages.private.asset.show', [
            'asset'    => $asset,
            'branches' => $listBranches->execute(),
            'services' => $listServices->execute(),
        ]);
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

    public function delete(int $assetId, AssetRepositoryInterface $assetRepo, DeleteAsset $useCase)
    {
        $asset = $assetRepo->findById($assetId);
        abort_if(!$asset, 404);

        $this->authorize('destroy', $asset);

        $useCase->execute($assetId, Auth::id());

        return back()->with('success', "Asset '{$asset->name}' deleted successfully.");
    }

    public function restore()
    {
        return back();
    }

    public function book(GetAssetRequest $request, GetAsset $useCase, GetService $getService)
    {
        $asset   = $useCase->execute($request->validated('asset_id'), Auth::user());
        $service = $getService->execute($request->validated('service_id'), Auth::user());
        return view('pages.public.asset.book', compact('asset', 'service'));
    }
}
