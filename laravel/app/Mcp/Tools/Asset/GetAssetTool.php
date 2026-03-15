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
class GetAssetTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Retrieves a single asset by ID with full details.

        Returns: id, name, description, and lists of related services and branches.

        Traversal:
        - Use GetServiceTool with any service id from the services list to get full service details.
        - Use GetBranchTool with any branch id from the branches list to get full branch details.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $asset = Asset::with([
            'services:id,name,is_active,price,duration_minutes',
            'branches:id,name,city,country,is_active',
        ])
            ->find($validated['id']);

        if (!$asset) {
            return Response::text("No asset found with ID {$validated['id']}.");
        }

        return Response::text($asset->toJson());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The unique identifier of the asset.'),
        ];
    }
}
