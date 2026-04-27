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
>>>>>>> 97c82cc ([FEAT] Added exception handling for Beanch and Service models.)
    }

    public function show(int $businessId, GetBusiness $useCase)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $business = $useCase->execute($businessId, Auth::user());
        return view('web.manage.business.show', compact('business'));
=======
        try {
            $business = $useCase->execute($businessId, Auth::user());
            return view('web.manage.business.show', compact('business'));
        } catch (BusinessNotFoundException $e) {
            return redirect()->route('manage.business.index')->with('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            return redirect()->route('manage.business.index')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
>>>>>>> 39297f8 ([FEAT] Reworked Business controller methods to return JSON, except the views.)
=======
        $business = $useCase->execute($businessId, Auth::user());
        return view('web.manage.business.show', compact('business'));
>>>>>>> 97c82cc ([FEAT] Added exception handling for Beanch and Service models.)
    }

    public function store(StoreBusinessRequest $request, StoreBusiness $useCase)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $business = $useCase->execute(StoreBusinessDTO::fromRequest($request), Auth::user());
        return response()->json(['message' => "Business '{$business->name}' created successfully.", 'data' => $business], 201);
=======
        try {
            $business = $useCase->execute(StoreBusinessDTO::fromRequest($request), Auth::user());
            return response()->json(['message' => "Business '{$business->name}' created successfully."]);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (BusinessCreationFailedException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
>>>>>>> 39297f8 ([FEAT] Reworked Business controller methods to return JSON, except the views.)
=======
        $business = $useCase->execute(StoreBusinessDTO::fromRequest($request), Auth::user());
        return response()->json(['message' => "Business '{$business->name}' created successfully.", 'data' => $business], 201);
>>>>>>> 97c82cc ([FEAT] Added exception handling for Beanch and Service models.)
    }

    public function update(int $businessId, UpdateBusinessRequest $request, UpdateBusiness $useCase)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $business = $useCase->execute(UpdateBusinessDTO::fromRequest($businessId, $request), Auth::user());
        return response()->json(['message' => "Business '{$business->name}' updated successfully.", 'data' => $business]);
=======
        try {
            $useCase->execute(UpdateBusinessDTO::fromRequest($businessId, $request), Auth::user());
            return response()->json(['message' => 'Business updated successfully!']);
        } catch (BusinessNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
>>>>>>> 39297f8 ([FEAT] Reworked Business controller methods to return JSON, except the views.)
=======
        $business = $useCase->execute(UpdateBusinessDTO::fromRequest($businessId, $request), Auth::user());
        return response()->json(['message' => "Business '{$business->name}' updated successfully.", 'data' => $business]);
>>>>>>> 97c82cc ([FEAT] Added exception handling for Beanch and Service models.)
    }

    public function delete(int $businessId, DeleteBusiness $useCase)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $useCase->execute($businessId, Auth::user());
        return response()->json(['message' => 'Business deleted successfully.', 'data' => $businessId]);
=======
        try {
            $useCase->execute($businessId, Auth::user());
            return response()->json(['message' => 'Business deleted successfully.']);
        } catch (BusinessNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
>>>>>>> 39297f8 ([FEAT] Reworked Business controller methods to return JSON, except the views.)
=======
        $useCase->execute($businessId, Auth::user());
        return response()->json(['message' => 'Business deleted successfully.', 'data' => $businessId]);
>>>>>>> 97c82cc ([FEAT] Added exception handling for Beanch and Service models.)
    }

    public function restore(int $businessId, RestoreBusiness $useCase)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $business = $useCase->execute($businessId, Auth::user());
        return response()->json(['message' => 'Business restored successfully.', 'data' => $business]);
    }
}
=======
>>>>>>> 39297f8 ([FEAT] Reworked Business controller methods to return JSON, except the views.)
        try {
            $useCase->execute($businessId, Auth::user());
            return response()->json(['message' => 'Business restored successfully.']);
        } catch (BusinessNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
<<<<<<< HEAD
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
=======
>>>>>>> 39297f8 ([FEAT] Reworked Business controller methods to return JSON, except the views.)
=======
        $business = $useCase->execute($businessId, Auth::user());
        return response()->json(['message' => 'Business restored successfully.', 'data' => $business]);
>>>>>>> 97c82cc ([FEAT] Added exception handling for Beanch and Service models.)
    }
}