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
class GetBranchTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Retrieves a single branch by ID with full address and related data.

        Returns: id, business_id, name, type, address, city, postal_code, country, is_active,
                 and a list of services offered at this branch (id, name, is_active).

        Traversal:
        - Use GetBusinessTool with business_id to fetch the parent business.
        - Use ListUsersForModelTool with model_type="branch" and this branch's id for assigned staff.
        - Use ListServicesTool with branch_id to get full service details at this branch.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $branch = Branch::with([
            'business:id,name,state',
            'services:id,name,is_active,price,duration_minutes',
        ])
            ->find($validated['id']);

        if (!$branch) {
            return Response::text("No branch found with ID {$validated['id']}.");
        }

        return Response::text($branch->toJson());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The unique identifier of the branch.'),
        ];
    }
}
