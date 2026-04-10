<?php

namespace App\Http\Controllers\Web;

use App\Application\DTO\SearchDTO;
use App\Application\Business\UseCases\GetBusiness;
use App\Application\UseCases\SearchEntities;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function __construct() {}

    public function index(Request $request, SearchEntities $searchUseCase)
    {
        $defaultTarget = match ($request->route()->getName()) {
            'public.services.index' => 'service',
            'public.branches.index' => 'branch',
            default                 => 'business',
        };

        $params = $request->all();
        if (!isset($params['target'])) {
            $params['target'] = $defaultTarget;
        }

        $dto = SearchDTO::fromRequest($params);
        $searchData = $searchUseCase->execute($dto);

        return view('web.customer.search.index', array_merge($searchData, [
            'dto'     => $dto,
            'filters' => $dto
        ]));
    }
}
