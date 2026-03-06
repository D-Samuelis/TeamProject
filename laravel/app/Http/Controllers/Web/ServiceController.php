<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Http\Requests\Business\StoreServiceRequest;

use App\Application\Business\DTO\CreateServiceDTO;
use App\Application\Business\UseCases\CreateService;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function store(StoreServiceRequest $request, CreateService $useCase)
    {
        $dto = new CreateServiceDTO(
            $request->validated('business_id'),
            $request->validated('name'),
            $request->validated('description'),
            $request->validated('duration_minutes'),
            $request->validated('price'),
            $request->validated('location_type') ?? 'branch',
            $request->validated('is_active') ?? false,
            $request->validated('branch_ids') ?? []
        );

        $useCase->execute($dto, Auth::id());

        return back();
    }
}
