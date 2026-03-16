<?php

namespace App\Mcp\Tools\Business;

use App\Application\DTO\SearchDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
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
class ListBusinessesTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Lists businesses with optional filters.

        Filters:
        - query: keyword search (name, description, services, branches)
        - city: filter by branch city
        - maxPrice: maximum service price
        - maxDuration: maximum service duration
    MARKDOWN;

    public function __construct(
        private BusinessRepositoryInterface $repository
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'query'        => 'nullable|string',
            'city'         => 'nullable|string',
            'maxPrice'     => 'nullable|numeric',
            'maxDuration'  => 'nullable|integer',
            'perPage'      => 'nullable|integer',
        ]);

        $dto = new SearchDTO(
            query: $validated['query'] ?? null,
            city: $validated['city'] ?? null,
            maxPrice: $validated['maxPrice'] ?? null,
            maxDuration: $validated['maxDuration'] ?? null,
            perPage: $validated['perPage'] ?? 50,
        );

        $result = $this->repository->search($dto);

        return Response::text(json_encode([
            'items' => $result->items(),
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
            'query' => $schema->string()->description('Keyword search.'),
            'city' => $schema->string()->description('City of branch.'),
            'maxPrice' => $schema->number()->description('Maximum service price.'),
            'maxDuration' => $schema->integer()->description('Maximum service duration in minutes.'),
            'perPage' => $schema->integer()->description('Items per page. Default 50.')
        ];
    }
}
