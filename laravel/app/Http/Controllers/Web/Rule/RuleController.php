<?php

namespace App\Http\Controllers\Web\Rule;

use App\Application\Rule\DTO\CreateRuleDTO;
use App\Application\Rule\UseCases\CreateRule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rule\StoreRuleRequest;
use Illuminate\Support\Facades\Auth;

class RuleController extends Controller
{
    public function store(StoreRuleRequest $request, CreateRule $useCase)
    {
        $dto = new CreateRuleDTO(
            $request->validated('title'),
            $request->validated('description'),
            $request->validated('valid_from'),
            $request->validated('valid_to'),
            $request->validated('rule_set'),
            $request->validated('asset_id')
        );

        $useCase->execute($dto, Auth::id());

        return back();
    }
}
