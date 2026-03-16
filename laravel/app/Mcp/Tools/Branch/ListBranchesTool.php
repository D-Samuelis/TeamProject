<?php

namespace App\Mcp\Tools\Branch;

use App\Application\DTO\SearchDTO;
use App\Domain\Branch\Interfaces\BranchRepositoryInterface;
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
        Lists branches with optional filters.

        Filters:
        - query: keyword search (branch name, city, business name)
        - city: filter by city
        - businessId: filter branches belonging to a business
        - locationTypes: filter by branch type
    MARKDOWN;

    public function __construct(
        private BranchRepositoryInterface $repository
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'query' => 'nullable|string',
            'city' => 'nullable|string',
            'businessId' => 'nullable|integer',
            'locationTypes' => 'nullable|array',
            'locationTypes.*' => 'string',
            'perPage' => 'nullable|integer',
        ]);

        $dto = new SearchDTO(
            query: $validated['query'] ?? null,
            businessId: $validated['businessId'] ?? null,
            city: $validated['city'] ?? null,
            locationTypes: $validated['locationTypes'] ?? [],
            perPage: $validated['perPage'] ?? 50,
        );

        $result = $this->repository->search($dto);

        return Response::text(json_encode([
            'items' => collect($result->items())->map(fn($branch) => [
                'id' => $branch->id,
                'name' => $branch->name,
                'city' => $branch->city,
                'business_id' => $branch->business_id,
                'business_name' => $branch->business?->name,
                'type' => $branch->type,
            ]),
            'pagination' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ]
        ]));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Keyword search across branch name, city, and business name.'),
            'city' => $schema->string()->description('Filter by city.'),
            'businessId' => $schema->integer()->description('Filter by business ID.'),
            'locationTypes' => $schema->array(
                $schema->string()
            )->description('Branch types to filter by.'),
            'perPage' => $schema->integer()->description('Items per page. Default 50.'),
        ];
    }
}
