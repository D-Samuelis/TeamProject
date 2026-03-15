<?php

namespace App\Mcp\Tools\Asset;

use App\Models\Business\Asset;
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
class ListAssetsTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Lists assets with optional filters. Returns paginated results (50 per page).

        Filters:
        - name: partial match search on asset name
        - service_id: get all assets required by a specific service (queries junction table)
        - branch_id: get all assets belonging to a specific branch (queries junction table)
        - cursor: last seen ID for pagination — use next_cursor from previous response

        Typical use: after GetServiceTool, call this with service_id to see what assets that service requires.
        Or after GetBranchTool, call this with branch_id to see what assets are at that location.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'name'       => 'nullable|string',
            'service_id' => 'nullable|integer',
            'branch_id'  => 'nullable|integer',
            'cursor'     => 'nullable|integer',
        ]);

        $assets = Asset::query()
            ->when(!empty($validated['name']), fn($q) =>
            $q->where('name', 'like', '%' . $validated['name'] . '%')
            )
            ->when(!empty($validated['service_id']), fn($q) =>
            $q->whereHas('services', fn($q2) =>
            $q2->where('services.id', $validated['service_id'])
            )
            )
            ->when(!empty($validated['branch_id']), fn($q) =>
            $q->whereHas('branches', fn($q2) =>
            $q2->where('branches.id', $validated['branch_id'])
            )
            )
            ->when(!empty($validated['cursor']), fn($q) =>
            $q->where('id', '>', $validated['cursor'])
            )
            ->orderBy('id')
            ->limit(50)
            ->get();

        if ($assets->isEmpty()) {
            return Response::text('No assets found matching your filters.');
        }

        $result = [
            'items'       => $assets,
            'next_cursor' => $assets->count() === 50 ? $assets->last()->id : null,
        ];

        return Response::text(json_encode($result));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name'       => $schema->string()->description('Partial name search.'),
            'service_id' => $schema->integer()->description('Filter assets required by a specific service ID.'),
            'branch_id'  => $schema->integer()->description('Filter assets belonging to a specific branch ID.'),
            'cursor'     => $schema->integer()->description('Last seen ID for pagination.'),
        ];
    }
}
