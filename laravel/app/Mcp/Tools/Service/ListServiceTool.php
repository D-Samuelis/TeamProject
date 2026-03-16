<?php

namespace App\Mcp\Tools\Service;

use App\Application\DTO\SearchDTO;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
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
class ListServiceTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
Lists services with optional filters.

Filters:
- query: keyword search (service name, description, business name, branch city)
- city: filter by branch city
- maxPrice: maximum service price
- maxDuration: maximum service duration in minutes
- businessId: filter by business
- locationTypes: filter by service location types
MARKDOWN;

    public function __construct(
        private ServiceRepositoryInterface $repository
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'query' => 'nullable|string',
            'city' => 'nullable|string',
            'maxPrice' => 'nullable|numeric',
            'maxDuration' => 'nullable|integer',
            'businessId' => 'nullable|integer',
            'locationTypes' => 'nullable|array',
            'locationTypes.*' => 'string',
            'perPage' => 'nullable|integer',
        ]);

        $dto = new SearchDTO(
            query: $validated['query'] ?? null,
            businessId: $validated['businessId'] ?? null,
            city: $validated['city'] ?? null,
            maxPrice: $validated['maxPrice'] ?? null,
            maxDuration: $validated['maxDuration'] ?? null,
            locationTypes: $validated['locationTypes'] ?? [],
            perPage: $validated['perPage'] ?? 50
        );

        $result = $this->repository->search($dto);

        return Response::text(json_encode([
            'items' => collect($result->items())->map(fn($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'duration_minutes' => $service->duration_minutes,
                'location_type' => $service->location_type,
                'business_id' => $service->business_id,
                'business_name' => $service->business?->name,
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
            'query' => $schema->string()->description('Keyword search across service name, description, business name, or branch city.'),
            'city' => $schema->string()->description('Filter services available in a specific city.'),
            'maxPrice' => $schema->number()->description('Maximum service price.'),
            'maxDuration' => $schema->integer()->description('Maximum duration in minutes.'),
            'businessId' => $schema->integer()->description('Filter by business ID.'),
            'locationTypes' => $schema->array(
                $schema->string()
            )->description('Filter by service location type.'),
            'perPage' => $schema->integer()->description('Items per page (default 50).'),
        ];
    }
}
