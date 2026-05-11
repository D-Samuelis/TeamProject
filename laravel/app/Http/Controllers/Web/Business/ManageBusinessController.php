<?php

namespace App\Http\Controllers\Web\Business;

use App\Application\DTO\BusinessSearchDTO;
use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Business\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Business\StoreBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;

use App\Application\Business\DTO\StoreBusinessDTO;
use App\Application\Business\DTO\UpdateBusinessDTO;

use App\Application\Business\UseCases\StoreBusiness;
use App\Application\Business\UseCases\DeleteBusiness;
use App\Application\Business\UseCases\GetBusiness;
use App\Application\Business\UseCases\ListBusinesses;
use App\Application\Business\UseCases\RestoreBusiness;
use App\Application\Business\UseCases\UpdateBusiness;

class ManageBusinessController extends Controller
{
    public function index(Request $request, ListBusinesses $useCase)
    {
<<<<<<< HEAD
        $user = Auth::user();

        try {
            $dto = BusinessSearchDTO::fromArray($request->query());
            $paginator = $useCase->execute($dto, $user);

            if ($request->wantsJson()) {
                return response()->json([
                    'data' => $paginator->items(),
                    'meta' => [
                        'current_page' => $paginator->currentPage(),
                        'last_page'    => $paginator->lastPage(),
                        'per_page'     => $paginator->perPage(),
                        'total'        => $paginator->total(),
                    ],
                ]);
            }

            return view('web.manage.business.index', [
                'businesses'  => $paginator->getCollection(),
                'meta'         => [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                ],
                'selectedUser'     => $request->user_id ? User::find((int) $request->user_id, ['id', 'name', 'email']) : null,
            ]);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('login');
        } catch (\Throwable $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
=======
        return view('web.manage.business.index', [
            'activeBusinesses'  => $useCase->execute(Auth::user(), 'active'),
            'deletedBusinesses' => $useCase->execute(Auth::user(), 'deleted'),
        ]);
>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
    }

    public function show(int $businessId, GetBusiness $useCase)
    {
        $business = $useCase->execute($businessId, Auth::user());
<<<<<<< HEAD
=======

        if (request()->expectsJson()) {
            return response()->json(['data' => $business]);
        }

>>>>>>> 9b2034c34521c9a6ab3916fb5b482b8336129fbf
        return view('web.manage.business.show', compact('business'));
    }

    public function store(StoreBusinessRequest $request, StoreBusiness $useCase)
    {
        $business = $useCase->execute(StoreBusinessDTO::fromRequest($request), Auth::user());
        return response()->json(['message' => "Business '{$business->name}' created successfully.", 'data' => $business], 201);
    }

    public function update(int $businessId, UpdateBusinessRequest $request, UpdateBusiness $useCase)
    {
        $business = $useCase->execute(UpdateBusinessDTO::fromRequest($businessId, $request), Auth::user());
        return response()->json(['message' => "Business '{$business->name}' updated successfully.", 'data' => $business]);
    }

    public function delete(int $businessId, DeleteBusiness $useCase)
    {
        $useCase->execute($businessId, Auth::user());
        return response()->json(['message' => 'Business deleted successfully.', 'data' => $businessId]);
    }

    public function restore(int $businessId, RestoreBusiness $useCase)
    {
        $business = $useCase->execute($businessId, Auth::user());
        return response()->json(['message' => 'Business restored successfully.', 'data' => $business]);
    }

    public function search(Request $request, ListBusinesses $listBusinesses): JsonResponse
    {
        $query = $request->query('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $dto = BusinessSearchDTO::fromArray([
            'business_name' => $query,
            'per_page'      => 8,
        ]);

        $results = $listBusinesses->execute($dto, Auth::user())
            ->getCollection()
            ->map(fn($b) => ['id' => $b->id, 'name' => $b->name]);

        return response()->json($results);
    }
}