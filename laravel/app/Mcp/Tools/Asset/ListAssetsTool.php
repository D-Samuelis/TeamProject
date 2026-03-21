<?php

namespace App\Mcp\Tools\Asset;

use App\Application\Asset\UseCases\ListAssets;
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
        This tool retrieves a list of assets from the system based on a search query.

        Assets may represent commodity (for example "Table", "Chair" or "Room"),
        employee (for example name like "Josh" or role like "masseur").

        ## When to use
        Use this tool when you need to find, look up, or browse assets.
        Use this only after you know what branch, business or service user wants.
        - If you don't have this context, use other tools: ListBusinessesTool, ListBranchesTool, ListServicesTool or ask user for more context.

        ## Required parameters
        - None

        ## Optional parameters
        - `q`: Search query to filter assets by name or description.
        - `per_page`: Number of results per page (default: 10).
        - `page`: Page number (default: 1).
        - `business_id`: The ID of the business to list assets for.

        ## Example use case
        User wants to book a massage at a specific business.
        Use this tool to find assets like massage tables or therapists ("masseur", "Josh")
    MARKDOWN;

    public function __construct(
        private readonly ListAssets $listAssets,
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'business_id' => 'nullable|integer',
            'q'           => 'nullable|string',
            'per_page'    => 'nullable|integer|min:1|max:100',
            'page'        => 'nullable|integer|min:1',
        ]);

        $assets = $this->listAssets->execute([
            'target'      => 'asset',
            'business_id' => $validated['business_id'] ?? null,
            'q'           => $validated['q'] ?? null,
            'per_page'    => $validated['per_page'] ?? 10,
            'page'        => $validated['page'] ?? 1,
        ]);

        return Response::text(
            $assets->map(function ($item) {
                return "id: " . $item['id'] . " name: " . $item['name'] . " description: " . $item['description'];
            })
        );
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'business_id' => $schema->integer('The ID of the business whose assets to list.'),
            'q'           => $schema->string('Optional search query to filter assets by name or description.'),
            'per_page'    => $schema->integer('Number of results per page. Defaults to 10.'),
            'page'        => $schema->integer('Page number for pagination. Defaults to 1.'),
        ];
    }
}
