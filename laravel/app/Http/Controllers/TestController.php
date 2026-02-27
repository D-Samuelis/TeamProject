<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Domain\Business\Entities\Branch;
use App\Domain\Business\Entities\Business;
use App\Domain\Business\Entities\Service;
use App\Enums\BusinessRole;

class TestController extends Controller
{
    public function index()
    {
        return view('archive.test-admin', [
            'businesses' => Business::with([
                'branches',
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

            $business->users()->attach(auth()->id(), ['role' => BusinessRole::OWNER->value]);
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
                ->wherePivot('role', BusinessRole::OWNER->value)
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
                    ->wherePivot('role', BusinessRole::OWNER->value)
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
}
