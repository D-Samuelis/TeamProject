<?php

namespace App\Mcp\Tools\Service;

use App\Models\Business\Service;
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
class ListServiceTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Lists services with optional filters. Returns paginated results (50 per page).

        Filters:
        - business_id: get all services belonging to a specific business
        - branch_id: get all services offered at a specific branch (queries junction table)
        - location_type: filter by delivery method (e.g. "in_person", "remote")
        - is_active: filter by active status
        - max_price: only return services at or below this price
        - cursor: last seen ID for pagination

        Typical use: after GetBranchTool, call this with branch_id to see what it offers.
        Or after GetBusinessTool, call this with business_id to see all its services.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'business_id'   => 'nullable|integer',
            'branch_id'     => 'nullable|integer',
            'location_type' => 'nullable|string',
            'is_active'     => 'nullable|boolean',
            'max_price'     => 'nullable|numeric',
            'cursor'        => 'nullable|integer',
        ]);

        $services = Service::query()
            ->when(!empty($validated['business_id']), fn($q) =>
            $q->where('business_id', $validated['business_id'])
            )
            ->when(!empty($validated['branch_id']), fn($q) =>
            $q->whereHas('branches', fn($q2) =>
            $q2->where('branches.id', $validated['branch_id'])
            )
            )
            ->when(!empty($validated['location_type']), fn($q) =>
            $q->where('location_type', $validated['location_type'])
            )
            ->when(isset($validated['is_active']), fn($q) =>
            $q->where('is_active', $validated['is_active'])
            )
            ->when(!empty($validated['max_price']), fn($q) =>
            $q->where('price', '<=', $validated['max_price'])
            )
            ->when(!empty($validated['cursor']), fn($q) =>
            $q->where('id', '>', $validated['cursor'])
            )
            ->with('business:id,name')
            ->orderBy('id')
            ->limit(50)
            ->get();

        if ($services->isEmpty()) {
            return Response::text('No services found matching your filters.');
        }

        $result = [
            'items'       => $services,
            'next_cursor' => $services->count() === 50 ? $services->last()->id : null,
        ];

        return Response::text(json_encode($result));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'business_id'   => $schema->integer()->description('Filter services by parent business ID.'),
            'branch_id'     => $schema->integer()->description('Filter services offered at a specific branch ID.'),
            'location_type' => $schema->string()->description('Delivery method e.g. "in_person", "remote".'),
            'is_active'     => $schema->boolean()->description('Filter by active status.'),
            'max_price'     => $schema->number()->description('Maximum price (inclusive).'),
            'cursor'        => $schema->integer()->description('Last seen ID for pagination.'),
        ];
    }
}
