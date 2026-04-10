<?php

namespace App\Http\Controllers\Web\Book;

use App\Http\Controllers\Controller;
use App\Application\Business\UseCases\GetBusiness;
use App\Application\Service\UseCases\GetService;
use App\Application\Asset\UseCases\GetAsset;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function business(int $businessId, GetBusiness $useCase)
    {
        $business = $useCase->execute($businessId);

        return view('web.customer.book.book', [
            'mode'     => 'business',
            'business' => $business,
        ]);
    }

    public function service(int $businessId, int $serviceId, GetService $useCase)
    {
        $service = $useCase->execute($serviceId);

        return view('web.customer.book.book', [
            'mode'       => 'service',
            'businessId' => $businessId,
            'service'    => $service,
        ]);
    }

    public function asset(int $businessId, int $serviceId, int $assetId, GetAsset $getAsset, GetService $getService)
    {

        $asset   = $getAsset->execute($assetId, Auth::user());
        $service = $getService->execute($serviceId);

        return view('web.customer.book.book', [
            'mode'       => 'asset',
            'businessId' => $businessId,
            'serviceId'  => $serviceId,
            'asset'      => $asset,
            'service'    => $service,
        ]);
    }
}