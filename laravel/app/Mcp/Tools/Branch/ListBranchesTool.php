<?php

namespace App\Mcp\Tools\Branch;

use App\Models\Business\Branch;
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
class ListBranchesTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Lists branches with optional filters. Returns paginated results (50 per page).

        Filters:
        - business_id: get all branches belonging to a specific business
        - service_id: get all branches that offer a specific service (queries junction table)
        - city: filter by city name (exact match)
        - country: filter by country (exact match)
        - is_active: filter by active status
        - cursor: last seen ID for pagination

        Typical use: after GetBusinessTool, call this with business_id to find its locations.
        Or after GetServiceTool, call this with service_id to find where a service is offered.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'business_id' => 'nullable|integer',
            'service_id'  => 'nullable|integer',
            'city'        => 'nullable|string',
            'country'     => 'nullable|string',
            'is_active'   => 'nullable|boolean',
            'cursor'      => 'nullable|integer',
        ]);

        $branches = Branch::query()
            ->when(!empty($validated['business_id']), fn($q) =>
            $q->where('business_id', $validated['business_id'])
            )
            ->when(!empty($validated['service_id']), fn($q) =>
            $q->whereHas('services', fn($q2) =>
            $q2->where('services.id', $validated['service_id'])
            )
            )
            ->when(!empty($validated['city']), fn($q) =>
            $q->where('city', $validated['city'])
            )
            ->when(!empty($validated['country']), fn($q) =>
            $q->where('country', $validated['country'])
            )
            ->when(isset($validated['is_active']), fn($q) =>
            $q->where('is_active', $validated['is_active'])
            )
            ->when(!empty($validated['cursor']), fn($q) =>
            $q->where('id', '>', $validated['cursor'])
            )
            ->with('business:id,name')
            ->orderBy('id')
            ->limit(50)
            ->get();

        if ($branches->isEmpty()) {
            return Response::text('No branches found matching your filters.');
        }

        $result = [
            'items'       => $branches,
            'next_cursor' => $branches->count() === 50 ? $branches->last()->id : null,
        ];

        return Response::text(json_encode($result));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'business_id' => $schema->integer()->description('Filter branches by parent business ID.'),
            'service_id'  => $schema->integer()->description('Filter branches that offer a specific service ID.'),
            'city'        => $schema->string()->description('Filter by city (exact match).'),
            'country'     => $schema->string()->description('Filter by country (exact match).'),
            'is_active'   => $schema->boolean()->description('Filter by active status.'),
            'cursor'      => $schema->integer()->description('Last seen ID for pagination.'),
        ];
    }
}
