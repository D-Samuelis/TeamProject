<?php

namespace App\Http\Controllers\Web\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Application\Branch\UseCases\StoreBranch;
use App\Application\Branch\UseCases\UpdateBranch;
use App\Application\Branch\UseCases\DeleteBranch;
use App\Application\Branch\UseCases\RestoreBranch;
use App\Application\Branch\UseCases\ListBranches;
use App\Application\Branch\UseCases\GetBranch;
use App\Application\Branch\DTO\StoreBranchDTO;
use App\Application\Branch\DTO\UpdateBranchDTO;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Service\UseCases\ListServices;

class PrivateBranchController extends Controller
{
    public function index(ListBranches $listBranches, ListBusinesses $listBusinesses)
    {
        return view('pages.private.branch.index', [
            'branches'   => $listBranches->execute(),
            'businesses' => $listBusinesses->execute(Auth::user()),
        ]);
    }

    public function show(int $branchId, GetBranch $getBranch, ListBusinesses $listBusinesses, ListServices $listServices)
    {
        $branch = $getBranch->execute($branchId);
        $branch->load('services', 'business', 'assets');

        return view('pages.private.branch.show', [
            'branch'     => $branch,
            'businesses' => $listBusinesses->execute(Auth::user()),
            'services'   => $listServices->execute(),
        ]);
    }

    public function store(StoreBranchRequest $request, StoreBranch $useCase)
    {
        $branch = $useCase->execute(StoreBranchDTO::fromRequest($request), Auth::id());
        return back()->with('success', "Branch '{$branch->name}' created.");
    }

    public function update(int $branchId, UpdateBranchRequest $request, UpdateBranch $useCase)
    {
        $useCase->execute(UpdateBranchDTO::fromRequest($branchId, $request), Auth::id());
        return back()->with('success', 'Branch updated successfully.');
    }

    public function delete(int $branchId, DeleteBranch $useCase)
    {
        $useCase->execute($branchId, Auth::id());
        return back()->with('success', 'Branch moved to trash.');
    }

    public function restore(int $branchId, RestoreBranch $useCase)
    {
        $useCase->execute($branchId, Auth::id());
        return back()->with('success', 'Branch restored.');
    }
}
