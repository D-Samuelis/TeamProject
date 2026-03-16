<?php

namespace App\Mcp\Tools\Asset;

use App\Application\DTO\SearchDTO;
use App\Domain\Asset\Interfaces\AssetRepositoryInterface;
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
        Lists assets with optional filters.

        Filters:
        - query: keyword search
        - businessId: filter by business
        - perPage: number of items per page
    MARKDOWN;

    public function __construct(
        private AssetRepositoryInterface $repository
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'query' => 'nullable|string',
            'businessId' => 'nullable|integer',
            'perPage' => 'nullable|integer',
        ]);

        $dto = new SearchDTO(
            query: $validated['query'] ?? null,
            businessId: $validated['businessId'] ?? null,
            perPage: $validated['perPage'] ?? 50
        );

        $assets = $this->repository->search($dto);

        return Response::text(json_encode([
            'items' => $assets->map(fn($asset) => [
                'id' => $asset->id,
                'name' => $asset->name ?? null,
                'type' => $asset->type ?? null,
                'url' => $asset->url ?? null,
                'business_id' => $asset->business_id ?? null,
                'created_at' => $asset->created_at,
            ])
        ]));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Keyword search for assets.'),
            'businessId' => $schema->integer()->description('Filter by business ID.'),
            'perPage' => $schema->integer()->description('Number of results per page (default 50).'),
        ];
    }
}
