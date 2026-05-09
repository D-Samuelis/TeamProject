<?php

namespace App\Http\Controllers\Web\User;

use App\Application\Auth\DTO\UpdateUserDTO;
use App\Application\Auth\Services\UserAuthorizationService;
use App\Application\Auth\UseCases\DeleteUser;
use App\Application\Auth\UseCases\UpdateUser;
use App\Application\DTO\UserSearchDTO;
use App\Application\User\UseCases\GetUser;
use App\Application\User\UseCases\ListUsers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Models\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Business\{Service, Branch};
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function index(Request $request, ListUsers $listUsers)
    {
        $user = Auth::user();
        $dto = UserSearchDTO::fromArray($request->query());
        $paginator = $listUsers->execute($dto, $user);

        return view('web.manage.users.index', [
            'users' => $paginator->getCollection(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    public function show(int $userId, GetUser $getUser)
    {
        $selected_user = $getUser->execute($userId, Auth::user());
        return view('web.manage.users.show', [
            'user' => $selected_user
        ]);
    }

    public function update(int $userId, UpdateUserRequest $request, UpdateUser $updateUser)
    {
        UserAuthorizationService::ensureCanUpdateUser(Auth::user());
        $updateUser->execute($userId, UpdateUserDTO::fromRequest($request));
        return back()->with('success', 'User updated successfully!');
    }

    public function delete(int $userId, DeleteUser $deleteUser)
    {
        UserAuthorizationService::ensureCanDeleteUser(Auth::user());
        $userToDelete = User::find($userId);
        $deleteUser->execute($userToDelete);
        return redirect()->route('manage.users.index')->with('success', 'User deleted successfully.');
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $user = Auth::user();

        $scope = $this->buildSearchScope($user);

        return $scope
            ->where(fn (Builder $q) =>
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
            )
            ->limit(8)
            ->get(['id', 'name', 'email'])
            ->pipe(fn ($results) => response()->json($results));
    }

    private function buildSearchScope(User $user): Builder
    {
        if ($user->isAdmin()) {
            return User::query();
        }

        $businessIds = $user->businesses()->pluck('businesses.id');
        $branchIds   = $user->branches()->pluck('branches.id');
        $serviceIds  = $user->services()->pluck('services.id');

        abort_if($businessIds->isEmpty() && $branchIds->isEmpty() && $serviceIds->isEmpty(), 403);

        $allBranchIds  = Branch::whereIn('business_id', $businessIds)->pluck('id')
            ->merge($branchIds)->unique();
        $allServiceIds = Service::whereIn('branch_id', $allBranchIds)->pluck('id')
            ->merge($serviceIds)->unique();

        return User::query()
            ->whereHas('appointments', fn (Builder $q) =>
            $q->whereIn('service_id', $allServiceIds)
            );
    }
}
