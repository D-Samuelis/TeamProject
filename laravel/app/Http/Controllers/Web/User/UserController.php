<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::query()
            ->where('email', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->limit(8)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }
}
