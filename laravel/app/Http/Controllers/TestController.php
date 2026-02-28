<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Domain\Business\Entities\Branch;
use App\Domain\Business\Entities\Business;
use App\Domain\Business\Entities\Service;
use App\Domain\Business\Entities\Asset;
use App\Domain\Business\Enums\BusinessRoleEnum;

class TestController extends Controller
{
    public function index()
    {
        return view('archive.test-admin', [
            'businesses' => Business::with([
                'branches',
                'branches.assets',
                'services.assets',
                'services.branches'
            ])->get(),
        ]);
    }

    public function storeBusiness(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $business = Business::create($validated);

            $business->users()->attach(auth()->id(), ['role' => BusinessRoleEnum::OWNER->value]);
        });

        return back();
    }

    public function storeBranch(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:physical,online,hybrid',
            'address_line_1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
        ]);

        $business = Business::findOrFail($validated['business_id']);

        abort_unless(
            $business
                ->users()
                ->where('user_id', auth()->id())
                ->wherePivot('role', BusinessRoleEnum::OWNER->value)
                ->exists(),
            403
        );

        Branch::create($validated);

        return back();
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branch_ids' => 'array',
        ]);

        DB::transaction(function () use ($validated) {
            $business = Business::findOrFail($validated['business_id']);

            // Only business owners can create services
            abort_unless(
                $business
                    ->users()
                    ->where('user_id', auth()->id())
                    ->wherePivot('role', BusinessRoleEnum::OWNER->value)
                    ->exists(),
                403
            );

            $service = Service::create([
                'business_id' => $business->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['branch_ids'])) {
                // Only attach branches that belong to this business
                $validBranchIds = Branch::where('business_id', $business->id)
                    ->whereIn('id', $validated['branch_ids'])
                    ->pluck('id')
                    ->toArray();

                $service->branches()->attach($validBranchIds);
            }
        });

        return back();
    }

    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branch_ids' => 'array',
            'branch_ids.*' => 'exists:branches,id',
            'services_ids' => 'array',
            'services_ids.*' => 'exists:services,id',
        ]);

        return DB::transaction(function () use ($validated) {
            $asset = Asset::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            if (!empty($validated['branch_ids'])) {
                $validBranchIds = Branch::whereIn('id', $validated['branch_ids'])
                    ->pluck('id');

                $asset->branches()->attach($validBranchIds);
            }

            if (!empty($validated['services_ids'])) {
                $validServiceIds = Service::whereIn('id', $validated['services_ids'])
                    ->pluck('id');

                $asset->services()->attach($validServiceIds);
            }

            return back()->with('success', 'Asset created and assigned successfully.');
        });
    }
}
