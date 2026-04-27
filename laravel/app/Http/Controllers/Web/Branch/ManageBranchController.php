<?php

namespace App\Http\Controllers\Web\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;

use App\Application\Branch\DTO\StoreBranchDTO;
use App\Application\Branch\DTO\UpdateBranchDTO;

use App\Application\Branch\UseCases\StoreBranch;
use App\Application\Branch\UseCases\UpdateBranch;
use App\Application\Branch\UseCases\DeleteBranch;
use App\Application\Branch\UseCases\RestoreBranch;
use App\Application\Branch\UseCases\ListBranches;
use App\Application\Branch\UseCases\GetBranch;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Service\UseCases\ListServices;

class ManageBranchController extends Controller
{
    public function index(ListBranches $listBranches, ListBusinesses $listBusinesses)
    {
        return view('web.manage.branch.index', [
            'branches'   => $listBranches->execute(Auth::user()),
            'businesses' => $listBusinesses->execute(Auth::user()),
        ]);
    }

    public function show(int $branchId, GetBranch $getBranch, ListBusinesses $listBusinesses, ListServices $listServices)
    {
        $branch = $getBranch->execute($branchId, Auth::user());
        return view('web.manage.branch.show', [
            'branch'     => $branch,
            'businesses' => $listBusinesses->execute(Auth::user()),
            'services'   => $listServices->execute(Auth::user(), $branch->business),
        ]);
    }

    public function store(StoreBranchRequest $request, StoreBranch $useCase)
    {
        $branch = $useCase->execute(StoreBranchDTO::fromRequest($request), Auth::user());
        return response()->json(['message' => "Branch '{$branch->name}' created successfully.", 'data' => $branch], 201);
    }

    public function update(int $branchId, UpdateBranchRequest $request, UpdateBranch $useCase)
    {
        $branch = $useCase->execute(UpdateBranchDTO::fromRequest($branchId, $request), Auth::user());
        return response()->json(['message' => "Branch '{$branch->name}' updated successfully.", 'data' => $branch]);
    }

    public function delete(int $branchId, DeleteBranch $useCase)
    {
        $useCase->execute($branchId, Auth::user());
        return response()->json(['message' => 'Branch deleted successfully.', 'data' => $branchId]);
    }

    public function restore(int $branchId, RestoreBranch $useCase)
    {
        $branch = $useCase->execute($branchId, Auth::user());
        return response()->json(['message' => 'Branch restored successfully.', 'data' => $branch]);
    }
}
