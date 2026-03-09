<?php

namespace App\Http\Controllers\Web\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\Business\DTO\SearchDTO;
use App\Application\Business\UseCases\SearchEntities;

class PublicBranchController extends Controller
{
    public function index(Request $request, SearchEntities $useCase)
    {
        $dto = SearchDTO::fromRequest($request->all(), 'branch');
        $results = $useCase->execute($dto);
        return view(
            'pages.public.manualBooking.index',
            ['branches' => $results, 'filters' => $dto]
        );
    }
}
