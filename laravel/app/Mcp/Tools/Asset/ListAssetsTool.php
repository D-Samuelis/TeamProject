<?php

namespace App\Mcp\Tools\Asset;

use App\Application\Asset\UseCases\ListAssets;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\ValidationException;
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

        Assets may either represent commodity (for example "Table", "Chair" or "Room"),
        or employees (for example name like "Josh" or role like "masseur").

        ## When to use
        Use this tool when you need to find, look up, or browse assets. Best to use this only when you have business_id.

        ## Required parameters
        - None

        ## Optional parameters
        - `per_page`: Number of results per page (default: 10).
        - `page`: Page number (default: 1).
        - `business_id`: The ID of the business to list assets for.

        ## Example use case
        - User wants to book a massage at a specific business.
        - Use this tool to find assets like massage tables or therapists ("masseur", "Josh")
    MARKDOWN;

    public function __construct(
        private readonly ListAssets $listAssets,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'business_id' => 'nullable|integer',
                'per_page'    => 'nullable|integer|min:1|max:100',
                'page'        => 'nullable|integer|min:1',
            ]);

            $assets = $this->listAssets->execute(
                filters: [
                    'target'      => 'asset',
                    'business_id' => $validated['business_id'] ?? null,
                    'per_page'    => $validated['per_page'] ?? 10,
                    'page'        => $validated['page'] ?? 1,
                ],
                user: null);

            return Response::text(
                $assets->map(function ($item) {
                    return "id: " . $item['id'] . " name: " . $item['name'] . " description: " . $item['description'];
                })
            );
        } catch (ValidationException $e) {
            logger()->warning('ListAssetsTool validation failed', ['errors' => $e->errors()]);

            return Response::text('Invalid input: ' . implode(' ', array_merge(...array_values($e->errors()))));
        } catch (\Throwable $e) {
            logger()->error('ListAssetsTool failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return Response::text('Failed to retrieve assets. Please try again later.');
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'business_id' => $schema->integer('The ID of the business whose assets to list.'),
            'per_page'    => $schema->integer('Number of results per page. Defaults to 10.'),
            'page'        => $schema->integer('Page number for pagination. Defaults to 1.'),
        ];
    }
}
