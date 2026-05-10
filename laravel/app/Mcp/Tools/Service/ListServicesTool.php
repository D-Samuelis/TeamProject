<?php

namespace App\Mcp\Tools\Service;

use App\Application\DTO\SearchDTO;
use App\Application\Service\UseCases\ListPublicServices;
use App\Domain\Service\Interfaces\ServiceRepositoryInterface;
use App\Models\Business\Service;
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
class ListServicesTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        This tool retrieves a list of services from the system based on a search query.

        Services represent the main activity provided to users (e.g. "Haircut", "Massage", "Yoga class").

        ## When to use
        Use this tool when you need to find, look up, or browse services.

        ## Required parameters
        None.

        ## Optional parameters
        - `q`: Keyword to search by service name, description, business name, or city.
        - `city`: Filter services available in a specific city.
        - `max_price`: Filter services up to a given price.
        - `max_duration`: Filter services up to a given duration in minutes.
        - `location_types`: Filter by location type (e.g. "online", "in_person").
        - `business_id`: Limit results to a specific business.
        - `per_page`: Number of results per page (default: 10).
        - `page`: Page number (default: 1).

        ## Example use case
        - User says "I want a massage in Bratislava under €50".
        - Use this tool with `q: "massage"`, `city: "Bratislava"`, `max_price: 50` to find matching services.
    MARKDOWN;

    public function __construct(
        private readonly ListPublicServices $listPublicServices,
    ) {}

    public function handle(Request $request): Response
    {
        try{
            logger()->debug('ListServicesTool called with request: ', ['request' => $request->all()]);
            $validated = $request->validate([
                'q'              => 'nullable|string',
                'city'           => 'nullable|string',
                'max_price'      => 'nullable|numeric|min:0',
                'max_duration'   => 'nullable|integer|min:1',
                'location_types' => 'nullable|array',
                'location_types.*' => 'string',
                'business_id'    => 'nullable|integer',
                'per_page'       => 'nullable|integer|min:1|max:100',
                'page'           => 'nullable|integer|min:1',
            ]);

            logger()->debug('ListServicesTool validated parameters: ', ['validated' => $validated]);

            $services = $this->listPublicServices->execute(
                user: null,
                business: null,
                scope: 'public',
                filters: [
                    'target'         => 'service',
                    'q'              => $validated['q'] ?? null,
                    'city'           => $validated['city'] ?? null,
                    'max_price'      => $validated['max_price'] ?? null,
                    'max_duration'   => $validated['max_duration'] ?? null,
                    'location_types' => $validated['location_types'] ?? [],
                    'business_id'    => $validated['business_id'] ?? null,
                    'per_page'       => $validated['per_page'] ?? 10,
                    'page'           => $validated['page'] ?? 1,
                ]
            );

            logger()->debug('ListServicesTool results: ', ['services' => $services]);

            return Response::text(
                $services->map(function ($item) {
                    return "service id: " . $item['id']
                        . ", service name: " . $item['name']
                        . ", service price: " . $item['price']
                        . ", service duration_minutes: " . $item['duration_minutes']
                        . ", service location_type: " . $item['location_type']
                        . ", business_id: " . $item['business_id']
                        . ", service description: " . $item['description'];
                })->implode("\n")
            );

        } catch (ValidationException $e) {
            logger()->warning('ListServicesTool validation failed', ['errors' => $e->errors()]);

            return Response::text('Invalid input: ' . implode(' ', array_merge(...array_values($e->errors()))));
        } catch (\Throwable $e) {
            logger()->error('ListServicesTool failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return Response::text('Failed to retrieve services. Please try again later.');
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'q'              => $schema->string('Keyword to search by service name, description, business name, or city.'),
            'city'           => $schema->string('Filter services available in a specific city.'),
            'max_price'      => $schema->number('Maximum price of the service.'),
            'max_duration'   => $schema->integer('Maximum duration of the service in minutes.'),
            'location_types' => $schema->array('Filter by location types (e.g. "online", "in_person").')->items($schema->string()),
            'business_id'    => $schema->integer('Limit results to a specific business ID.'),
            'per_page'       => $schema->integer('Number of results per page. Defaults to 10.'),
            'page'           => $schema->integer('Page number for pagination. Defaults to 1.'),
        ];
    }
}
