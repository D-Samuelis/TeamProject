<?php

namespace App\Http\Controllers\Web\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\Business\DTO\SearchDTO;
use App\Application\Business\UseCases\SearchEntities;

class PublicServiceController extends Controller
{
    public function index(Request $request, SearchEntities $useCase)
    {
        $dto = SearchDTO::fromRequest($request->all(), 'service');
        $results = $useCase->execute($dto);
        return view('pages.public.manualBooking.index', [
            'results' => $results,
            'filters' => $dto,
        ]);
    }
}
