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
class GetServiceTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Retrieves a single service by ID with full details.

        Returns: id, business_id, name, description, duration_minutes, price,
                 location_type, is_active, and lists of branches and assets.

        Traversal:
        - Use GetBusinessTool with business_id to fetch the owning business.
        - Use ListBranchesTool with service_id to find all branches offering this service.
        - Use ListUsersForModelTool with model_type="service" and this service's id for assigned staff.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $service = Service::with([
            'business:id,name,state',
            'branches:id,name,city,country,is_active',
            'assets:id,name',
        ])
            ->find($validated['id']);

        if (!$service) {
            return Response::text("No service found with ID {$validated['id']}.");
        }

        return Response::text($service->toJson());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The unique identifier of the service.'),
        ];
    }
}
