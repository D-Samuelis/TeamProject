<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Enums\BusinessRole;
use App\Models\Business;
use App\Models\Branch;
use App\Models\Service;

class TestController extends Controller
{
    public function index()
    {
        return view('archive.test-admin', [
            'businesses' => Business::with(['branches', 'services'])->get(),
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
        Branch::create([
            'business_id' => $request->business_id,
            'name' => $request->name,
            'address' => $request->address,
        ]);

        return back();
    }

    public function storeService(Request $request)
    {
        Service::create([
            'business_id' => $request->business_id,
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'description' => $request->description,
            'is_online' => $request->has('is_online'),
        ]);

        return back();
    }
}
