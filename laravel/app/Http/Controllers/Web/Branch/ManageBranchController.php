<?php

namespace App\Http\Controllers\Web\Branch;

use App\Application\DTO\BusinessSearchDTO;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

class ManageBranchController extends Controller
{
    public function index(ListBranches $listBranches, ListBusinesses $listBusinesses)
    {
        return view('web.manage.branch.index', [
            'branches'   => $listBranches->execute(Auth::user(), null, 'all'),
            'businesses' => $listBusinesses->execute(BusinessSearchDTO::fromArray([]), Auth::user())->getCollection(),
        ]);
    }

    public function show(int $branchId, GetBranch $getBranch, ListBusinesses $listBusinesses, ListServices $listServices)
    {
        $branch = $getBranch->execute($branchId, Auth::user());
        $dto = BusinessSearchDTO::fromArray([]);
        return view('web.manage.branch.show', [
            'branch'     => $branch,
            'businesses' => $listBusinesses->execute($dto, Auth::user())->getCollection(),
            'services'   => $listServices->execute(Auth::user(), $branch->business),
        ]);
    }

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

    public function search(Request $request): JsonResponse
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $businesses = User::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(8)
            ->get(['id', 'name']);

        return response()->json($businesses);
    }
}
