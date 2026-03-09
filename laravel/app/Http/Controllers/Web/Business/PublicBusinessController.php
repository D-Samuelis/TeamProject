<?php

namespace App\Http\Controllers\Web\Business;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Application\Business\DTO\SearchDTO;
use App\Application\Business\UseCases\GetBusiness;
use App\Application\Business\UseCases\SearchEntities;

class PublicBusinessController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of all published businesses (The Marketplace).
     */
    public function index(Request $request, SearchEntities $useCase)
    {
        $dto = SearchDTO::fromRequest($request->all());
        $results = $useCase->execute($dto);
        return view('pages.public.manualBooking.index', [
            'results' => $results,
            'filters' => $dto,
        ]);
    }

    /**
     * Display a specific business profile to the public.
     */
    public function show(int $businessId, GetBusiness $useCase)
    {
        $business = $useCase->execute($businessId, Auth::user());
        return view('pages.public.manualBooking.show', compact('business'));
    }
}
