<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Branch\StoreBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Application\Branch\UseCases\CreateBranch;
use App\Application\Branch\UseCases\UpdateBranch;
use App\Application\Branch\UseCases\DeleteBranch;
use App\Application\Branch\UseCases\RestoreBranch;
use App\Application\Branch\DTO\CreateBranchDTO;
use App\Application\Branch\DTO\UpdateBranchDTO;

class BranchController extends Controller
{
    public function store(int $businessId, StoreBranchRequest $request, CreateBranch $useCase)
    {
        $branch = $useCase->execute(CreateBranchDTO::fromRequest($businessId, $request), Auth::id());
        return back()->with('success', "Branch '{$branch->name}' created.");
    }

    public function update(int $businessId, int $branchId, UpdateBranchRequest $request, UpdateBranch $useCase)
    {
        $useCase->execute(UpdateBranchDTO::fromRequest($branchId, $request), Auth::id());
        return back()->with('success', 'Branch updated successfully.');
    }

    public function delete(int $businessId, int $branchId, DeleteBranch $useCase)
    {
        $useCase->execute($branchId, Auth::id());
        return back()->with('success', 'Branch moved to trash.');
    }

    public function restore(int $businessId, int $branchId, RestoreBranch $useCase)
    {
        $useCase->execute($branchId, Auth::id());
        return back()->with('success', 'Branch restored.');
    }
}
