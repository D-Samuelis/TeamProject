<?php

namespace App\Http\Controllers\Web\Rule;

use App\Application\Rule\DTO\CreateRuleDTO;
use App\Application\Rule\DTO\UpdateRuleDTO;
use App\Application\Rule\UseCases\CreateRule;
use App\Application\Rule\UseCases\UpdateRule;
use App\Application\Rule\UseCases\DeleteRule;
use App\Application\Rule\UseCases\ReorderRule;
use App\Application\Rule\UseCases\ReorderAllRules;
use App\Domain\Rule\Interfaces\RuleRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Rule\StoreRuleRequest;
use App\Http\Requests\Rule\UpdateRuleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RuleController extends Controller
{
    public function store(StoreRuleRequest $request, CreateRule $useCase)
    {
        $dto = new CreateRuleDTO(
            title:       $request->validated('title'),
            description: $request->validated('description'),
            valid_from:  $request->validated('valid_from'),
            valid_to:    $request->validated('valid_to'),
            rule_set:    $request->validated('rule_set'),
            asset_id:    $request->validated('asset_id'),
        );

        $useCase->execute($dto, Auth::id());

        return back()->with('success', 'Rule created successfully.');
    }

    public function update(int $ruleId, UpdateRuleRequest $request, UpdateRule $useCase)
    {
        $dto = new UpdateRuleDTO(
            id:          $ruleId,
            title:       $request->validated('title'),
            description: $request->validated('description'),
            valid_from:  $request->validated('valid_from'),
            valid_to:    $request->validated('valid_to'),
            rule_set:    $request->validated('rule_set'),
        );

        $useCase->execute($dto, Auth::id());

        return back()->with('success', 'Rule updated successfully.');
    }

    public function delete(int $ruleId, DeleteRule $useCase)
    {
        $useCase->execute($ruleId, Auth::id());
        return back()->with('success', 'Rule deleted.');
    }

    public function reorder(int $ruleId, Request $request, ReorderRule $useCase)
    {
        $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        $useCase->execute($ruleId, $request->input('direction'), Auth::id());

        return back()->with('success', 'Rule order updated.');
    }

    public function reorderAll(Request $request, ReorderAllRules $useCase)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:rules,id',
        ]);

        $useCase->execute($request->input('order'), Auth::id());

        return response()->json(['status' => 'success']);
    }
}
