<?php

namespace App\Http\Controllers\Web\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\DTO\AssignUserDTO;
use App\Application\UseCases\AssignUser;
use App\Application\UseCases\RemoveUser;
use App\Application\UseCases\UpdateUserRole;

class BusinessAssignmentController extends Controller
{
    public function store(Request $request, int $businessId, AssignUser $useCase)
    {
        $request->validate([
            'email'       => 'required|email|exists:users,email',
            'role'        => 'required|string',
            'target_type' => 'required|in:business,branch,service',
            'target_id'   => 'required|integer',
        ]);

        $useCase->execute(AssignUserDTO::fromRequest($request, $businessId));
        return back()->with('success', 'User assigned and notified!');
    }

    public function update(Request $request, int $businessId, int $userId, UpdateUserRole $useCase)
    {
        $request->validate([
            'role'        => 'required|string',
            'target_type' => 'required|in:business,branch,service',
            'target_id'   => 'required|integer',
        ]);

        $updated = $useCase->execute($businessId, $userId, $request->role, $request->target_type, $request->target_id);
        return $updated ? back()->with('success', 'User role updated!') : back()->with('info', 'No changes made.');
    }

    public function delete(Request $request, int $businessId, int $userId, RemoveUser $useCase)
    {
        try {
            $useCase->execute($businessId, $userId, $request->input('target_type', 'business'), $request->input('target_id', $businessId));
            return back()->with('success', 'User removed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
