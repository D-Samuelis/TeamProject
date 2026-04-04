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
use App\Application\Branch\UseCases\AssignServiceToBranchUseCase;
use App\Application\Branch\UseCases\UnassignServiceFromBranchUseCase;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Service\UseCases\ListServices;
use App\Http\Requests\Branch\BranchServiceAssignmentRequest;

class ManageBranchController extends Controller
{
    // ── DATA PERSISTENCE ─────────────────────────────────────────

    public function store(StoreBranchRequest $request, StoreBranch $useCase)
    {
        $branch = $useCase->execute(StoreBranchDTO::fromRequest($request), Auth::user());
        return back()->with('success', "Branch '{$branch->name}' created.");
    }

    public function update(int $branchId, UpdateBranchRequest $request, UpdateBranch $useCase)
    {
        $branch = $useCase->execute(UpdateBranchDTO::fromRequest($branchId, $request), Auth::user());
        return back()->with('success', "Branch '{$branch->name}' updated successfully.");
    }

    public function delete(int $branchId, DeleteBranch $useCase)
    {
        $useCase->execute($branchId, Auth::user());
        return back()->with('success', 'Branch moved to trash.');
    }

    public function restore(int $branchId, RestoreBranch $useCase)
    {
        $useCase->execute($branchId, Auth::user());
        return back()->with('success', 'Branch restored.');
    }

    public function assignServices(
        BranchServiceAssignmentRequest $request,
        AssignServiceToBranchUseCase $useCase
    ) {
        $assigned = $useCase->execute($request->validated()['branch_id'], $request->validated()['service_ids']);
        return back()->with('success', count($assigned) . ' service(s) assigned.');
    }

    public function unassignServices(
        BranchServiceAssignmentRequest $request,
        UnassignServiceFromBranchUseCase $useCase
    ) {
        $count = $useCase->execute($request->validated()['branch_id'], $request->validated()['service_ids']);
        return back()->with('success', "$count service(s) unassigned.");
    }

    // ── VIEWS ─────────────────────────────────────────

    public function index(ListBranches $listBranches, ListBusinesses $listBusinesses)
    {
        return view('pages.branch.index', [
            'branches'   => $listBranches->execute(Auth::user()),
            'businesses' => $listBusinesses->execute(Auth::user()),
        ]);
    }

    public function show(int $branchId, GetBranch $useCase, ListServices $listServices)
    {
        $branch = $useCase->execute($branchId, Auth::user());
        return view('pages.branch.show', [
            'branch'     => $branch,
            'services'   => $listServices->execute(Auth::user(), $branch->business),
        ]);
    }
}
