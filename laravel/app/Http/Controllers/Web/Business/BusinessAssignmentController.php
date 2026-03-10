<?php

namespace App\Http\Controllers\Web\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// DTOs
use App\Application\Business\DTO\AssignUserDTO;

// Use Cases
use App\Application\Business\UseCases\AssignUser;
use App\Application\Business\UseCases\RemoveUser;
use App\Application\Business\UseCases\UpdateUserRole;

class BusinessAssignmentController extends Controller
{
    public function __construct() {}

    public function store(Request $request, int $businessId, AssignUser $useCase)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|string',
        ]);

        $useCase->execute(AssignUserDTO::fromRequest($request, $businessId));
        return back()->with('success', 'User assigned and notified!');
    }

    public function update(Request $request, int $businessId, int $userId, UpdateUserRole $useCase)
    {
        $request->validate(['role' => 'required|string']);

        $updated = $useCase->execute($businessId, $userId, $request->role);
        return $updated ? back()->with('success', 'User role updated and notified!') : back()->with('info', 'No changes made.');
    }

    public function delete(int $businessId, int $userId, RemoveUser $useCase)
    {
        try {
            $useCase->execute($businessId, $userId);
            return back()->with('success', 'User removed and notified.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
