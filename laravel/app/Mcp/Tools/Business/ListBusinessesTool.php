<?php

namespace App\Mcp\Tools\Business;

use App\Models\Business\Business;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly(true)]
#[IsDestructive(false)]
#[IsOpenWorld(false)]
#[IsIdempotent(true)]
class ListBusinessesTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Lists businesses with optional filters. Returns paginated results (50 per page).

        Filters:
        - name: partial match search on business name
        - state: filter by BusinessStateEnum value (e.g. "active", "suspended")
        - is_published: filter by published status (true/false)
        - cursor: last seen ID for pagination — use next_cursor from previous response

        After finding a business, use GetBusinessTool with its ID for full details.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'name'         => 'nullable|string',
            'state'        => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'cursor'       => 'nullable|integer',
            'description'  => 'nullable|string',
        ]);

        $businesses = Business::query()
            ->when(!empty($validated['name']), fn($q) =>
            $q->where('name', 'like', '%' . $validated['name'] . '%')
            )
            ->when(!empty($validated['description']), fn($q) =>
            $q->where('description', 'like', '%' . $validated['description'] . '%')
            )
            ->when(isset($validated['state']), fn($q) =>
            $q->where('state', $validated['state'])
            )
            ->when(isset($validated['is_published']), fn($q) =>
            $q->where('is_published', $validated['is_published'])
            )
            ->when(!empty($validated['cursor']), fn($q) =>
            $q->where('id', '>', $validated['cursor'])
            )
            ->withCount(['branches', 'services'])
            ->orderBy('id')
            ->limit(50)
            ->get();

        if ($businesses->isEmpty()) {
            return Response::text('No businesses found matching your filters.');
        }

        $result = [
            'items'       => $businesses,
            'next_cursor' => $businesses->count() === 50 ? $businesses->last()->id : null,
        ];

        return Response::text(json_encode($result));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name'         => $schema->string()->description('Partial name search.'),
            'state'        => $schema->string()->description('BusinessStateEnum value e.g. "active", "suspended".'),
            'is_published' => $schema->boolean()->description('Filter by published status.'),
            'cursor'       => $schema->integer()->description('Last seen ID for pagination.'),
            'description'  => $schema->string()->description('Business description.'),
        ];
    }
}
