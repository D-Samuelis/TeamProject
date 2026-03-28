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
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index(ListAssets $listAssets, ListBranches $listBranches, ListServices $listServices)
    {
        $user = Auth::user();
        [$branches, $services] = $this->getAssociatedBranchesAndServices($user, $listBranches, $listServices);

        return view('pages.asset.index', [
            'assets'   => $listAssets->execute([], $user),
            'branches' => $branches,
            'services' => $services,
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
        $user     = Auth::user();
        $asset = $getAsset->execute($assetId, $user);
        $asset->load('branches.business', 'services');
        [$branches, $services] = $this->getAssociatedBranchesAndServices($user, $listBranches, $listServices);

        return view('pages.asset.show', [
            'asset'    => $asset,
            'branches' => $branches,
            'services' => $services,
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

    private function getAssociatedBranchesAndServices(
        User $user,
        ListBranches $listBranches,
        ListServices $listServices
    ): array {
        $branches = $listBranches->execute($user);
        $services = $listServices->execute($user);

        if (! $user->isAdmin()) {
            $user->loadMissing(['branches', 'services']);

            $userBranchIds = $user->branches
                ->filter(fn($b) => $b->pivot->role !== 'staff')
                ->pluck('id');

            $userServiceIds = $user->services->pluck('id');

            $managedBranchIds = $branches
                ->filter(function ($branch) use ($user) {
                    $role = app(\App\Domain\User\Interfaces\UserRepositoryInterface::class)
                        ->getBusinessRole($user, $branch->business);
                    return in_array($role, [
                        \App\Domain\Business\Enums\BusinessRoleEnum::OWNER,
                        \App\Domain\Business\Enums\BusinessRoleEnum::MANAGER,
                    ]);
                })
                ->pluck('id');

            $allBranchIds = $userBranchIds->merge($managedBranchIds)->unique();

            $branches = $branches->filter(fn($b) => $allBranchIds->contains($b->id));
            $services = $services->filter(function ($s) use ($user, $userServiceIds, $allBranchIds) {
                // Only include direct service assignment if user is manager (not staff)
                if ($userServiceIds->contains($s->id)) {
                    $member = $user->services->firstWhere('id', $s->id);
                    if ($member && $member->pivot->role === 'staff') {
                        return false; // staff on service — exclude from create form
                    }
                    return true;
                }

                // Include if service belongs to a branch the user manages
                return $s->branches->pluck('id')->intersect($allBranchIds)->isNotEmpty();
            });
        }

        return [$branches, $services];
    }
}
