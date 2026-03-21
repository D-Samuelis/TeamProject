<?php

namespace App\Mcp\Tools\Branch;

use App\Application\Branch\UseCases\ListBranches;
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
        This tool retrieves a list of branches from the system based on a search query.

        Branches represent places where the service is performed — usually physically, but can also be 'online'.

        ## When to use
        Use this tool when you need to find, look up, or browse branches.
        Use this only after you know what business and service user wants.
        - If you don't have this context, use other tools: ListBusinessesTool, ListServicesTool or ask user for more context.

        ## Required parameters
        - None.

        ## Optional parameters
        - `business_id`: The ID of the business whose branches to list.
        - `q`: Search query to filter branches by name.
        - `city`: Filter branches by city.
        - `per_page`: Number of results per page (default: 10).
        - `page`: Page number (default: 1).

        ## Example use case
        - User wants to book a haircut in Bratislava — after identifying the business and service,
          use this tool with `business_id` and `city: "Bratislava"` to find the right branch.
    MARKDOWN;

    public function __construct(
        private readonly ListBranches $listBranches,
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'business_id' => 'nullable|integer',
            'q'           => 'nullable|string',
            'city'        => 'nullable|string',
            'per_page'    => 'nullable|integer|min:1|max:100',
            'page'        => 'nullable|integer|min:1',
        ]);

        $branches = $this->listBranches->execute([
            'target'      => 'branch',
            'business_id' => $validated['business_id'] ?? null,
            'q'           => $validated['q'] ?? null,
            'city'        => $validated['city'] ?? null,
            'per_page'    => $validated['per_page'] ?? 10,
            'page'        => $validated['page'] ?? 1,
        ]);

        return Response::text(
            $branches->map(function ($item) {
                return "id: " . $item['id']
                    . " name: " . $item['name']
                    . " city: " . $item['city']
                    . " address: " . $item['address_line_1'];
            })
        );
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'business_id' => $schema->integer('The ID of the business whose branches to list.'),
            'q'           => $schema->string('Optional search query to filter branches by name.'),
            'city'        => $schema->string('Optional city name to filter branches by location.'),
            'per_page'    => $schema->integer('Number of results per page. Defaults to 10.'),
            'page'        => $schema->integer('Page number for pagination. Defaults to 1.'),
        ];
    }
}
