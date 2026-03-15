<?php

namespace App\Mcp\Tools;

use App\Models\Business\Branch;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class SearchBranchTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        This tool allows users to search for available branches.

        Users can specify the branch name or type, such as "main office", "retail store", or "clinic".
        Users can also provide additional details to narrow down their search, such as city, country, or the business name.

        The tool will return a list of matching branches.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'keyword' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'business' => 'nullable|string',
        ]);

        if (empty($validated['keyword'])) {
            return Response::text('Please provide a keyword to search for branches.');
        }

        $query = Branch::query()
            ->where('is_active', true)
            ->where(function ($q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['keyword'] . '%');
            });

        if (!empty($validated['city'])) {
            $query->where('city', 'like', '%' . $validated['city'] . '%');
        }

        if (!empty($validated['country'])) {
            $query->where('country', 'like', '%' . $validated['country'] . '%');
        }

        if (!empty($validated['business'])) {
            $query->whereHas('business', function ($q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['business'] . '%');
            });
        }

        $branches = $query->limit(10)->get();

        if ($branches->isEmpty()) {
            return Response::text('No branches found matching your search.');
        }

        $result = $branches->map(function ($branch) {
            return "{$branch->name} ({$branch->type}) - {$branch->city}, {$branch->country}";
        })->implode("\n");

        return Response::text($result);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'keyword' => $schema->string()->description('The branch name.'),
            'city' => $schema->string()->description('The city where the branch is located.'),
            'country' => $schema->string()->description('The country where the branch is located.'),
            'business' => $schema->string()->description('The name of the business that owns the branch.'),
        ];
    }
}
