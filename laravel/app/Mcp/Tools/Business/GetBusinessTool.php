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
class GetBusinessTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Retrieves a single business by ID with full details.

        Returns: id, name, description, state, is_published, branch_count, service_count, user_count.

        Traversal:
        - Use ListBranchesTool with business_id to get all branches under this business.
        - Use ListServicesTool with business_id to get all services under this business.
        - Use ListUsersForModelTool with model_type="business" and this business's id to get assigned users.
    MARKDOWN;

    // TODO - don't expose everything
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $business = Business::query()
            ->withCount(['branches', 'services', 'users'])
            ->findOrFail($validated['id']);

        if (!$business) {
            return Response::text("No business found with ID {$validated['id']}.");
        }

        return Response::text($business->toJson());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The unique identifier of the business.'),
        ];
    }
}
