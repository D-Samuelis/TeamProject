<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Branch\UseCases\ListBranches;
use App\Application\Service\UseCases\ListServices;
use App\Application\Asset\UseCases\ListAssets;
use App\Application\DTO\BusinessSearchDTO;
use App\Application\DTO\BranchSearchDTO;
use App\Application\DTO\ServiceSearchDTO;
use App\Application\DTO\AssetSearchDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        ListBusinesses $listBusinesses,
        ListBranches $listBranches,
        ListServices $listServices,
        ListAssets $listAssets,
    ) {
        $user = Auth::user();

        $businesses = $listBusinesses->execute(BusinessSearchDTO::fromArray(['per_page' => 1000]), $user)->getCollection();
        $branches   = $listBranches->execute(BranchSearchDTO::fromArray(['per_page' => 1000]), $user)->getCollection();
        $services   = $listServices->execute(ServiceSearchDTO::fromArray(['per_page' => 1000]), $user)->getCollection();
        $assets     = $listAssets->execute(AssetSearchDTO::fromArray(['per_page' => 1000]), $user)->getCollection();

        $businesses->load(['branches']);
        $branches->load(['services', 'business']);

        // ── Business stats ───────────────────────────────────────────────────
        $businessStats = [
            'total'     => $businesses->count(),
            'published' => $businesses->filter(fn($b) => !$b->deleted_at && $b->is_published)->count(),
            'hidden'    => $businesses->filter(fn($b) => !$b->deleted_at && !$b->is_published)->count(),
            'archived'  => $businesses->filter(fn($b) => $b->deleted_at)->count(),
        ];

        // ── Branch stats ─────────────────────────────────────────────────────
        $branchStats = [
            'total'    => $branches->count(),
            'active'   => $branches->filter(fn($b) => !$b->deleted_at && $b->is_active)->count(),
            'inactive' => $branches->filter(fn($b) => !$b->deleted_at && !$b->is_active)->count(),
            'archived' => $branches->filter(fn($b) => $b->deleted_at)->count(),
        ];

        // ── Service stats ────────────────────────────────────────────────────
        $serviceStats = [
            'total'    => $services->count(),
            'active'   => $services->filter(fn($s) => !$s->deleted_at && $s->is_active)->count(),
            'inactive' => $services->filter(fn($s) => !$s->deleted_at && !$s->is_active)->count(),
            'archived' => $services->filter(fn($s) => $s->deleted_at)->count(),
        ];

        // ── Asset stats ──────────────────────────────────────────────────────
        $assetStats = [
            'total'    => $assets->count(),
            'active'   => $assets->filter(fn($a) => !$a->deleted_at && $a->is_active)->count(),
            'inactive' => $assets->filter(fn($a) => !$a->deleted_at && !$a->is_active)->count(),
            'archived' => $assets->filter(fn($a) => $a->deleted_at)->count(),
        ];

        // ── Coverage ─────────────────────────────────────────────────────────
        $activeBranches  = $branchStats['active'];
        $coveredBranches = $branches
            ->filter(fn($b) => !$b->deleted_at && $b->is_active && $b->services->isNotEmpty())
            ->count();
        $coverage = $activeBranches > 0
            ? (int) round(($coveredBranches / $activeBranches) * 100)
            : 0;

        return view('web.manage.dashboard', [
            'businesses'   => $businesses,
            'branches'     => $branches,
            'services'     => $services,
            'assets'       => $assets,
            'businessStats' => $businessStats,
            'branchStats'   => $branchStats,
            'serviceStats'  => $serviceStats,
            'assetStats'    => $assetStats,
            'coverage'      => $coverage,
        ]);
    }
}