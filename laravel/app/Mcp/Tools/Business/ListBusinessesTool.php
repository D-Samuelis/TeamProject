<?php

namespace App\Mcp\Tools\Business;

use App\Application\Business\UseCases\ListBusinesses;
use App\Application\DTO\SearchDTO;
use App\Domain\Business\Interfaces\BusinessRepositoryInterface;
use App\Models\Business\Business;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Auth;
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
class ListBusinessesTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        This tool retrieves a list of businesses from the system based on a search query.

        A business is the main entity that provides services, owns assets, and operates branches.

        ## When to use
        Use this tool when you need to find, look up, or browse businesses.
        Use this only after you know what branch, business or service user wants.
        - If you don't have this context, use other ListServicesTool tool or ask user for more context.


        ## Required parameters
        None.

        ## Optional parameters
        - `q`: Keyword to search by business name, description, branch city/name, or service name.
        - `city`: Filter businesses that have an active branch in a specific city.
        - `max_price`: Filter businesses that offer at least one service at or below this price.
        - `max_duration`: Filter businesses that offer at least one service within this duration (minutes).
        - `location_types`: Filter businesses offering services of specific location types (e.g. "online", "in_person").
        - `per_page`: Number of results per page (default: 10).
        - `page`: Page number (default: 1).

        ## Example use case
        User wants a massage in Bratislava — after identifying the service with ListServiceTool,
        call this tool with `q: "massage"` and `city: "Bratislava"` to find matching businesses.
    MARKDOWN;

    public function __construct(
        private readonly ListBusinesses $listBusinesses,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $user = Auth::user();

            logger()->debug('User: ', ['$user' => $user]);

            $validated = $request->validate([
                'q'                => 'nullable|string',
                'city'             => 'nullable|string',
                'max_price'        => 'nullable|numeric|min:0',
                'max_duration'     => 'nullable|integer|min:1',
                'location_types'   => 'nullable|array',
                'location_types.*' => 'string',
                'per_page'         => 'nullable|integer|min:1|max:100',
                'page'             => 'nullable|integer|min:1',
            ]);

            $businesses = $this->listBusinesses->execute(
                user: null,
                scope: 'public',
                filters: [
                    'target'         => 'business',
                    'q'              => $validated['q'] ?? null,
                    'city'           => $validated['city'] ?? null,
                    'max_price'      => $validated['max_price'] ?? null,
                    'max_duration'   => $validated['max_duration'] ?? null,
                    'location_types' => $validated['location_types'] ?? [],
                    'per_page'       => $validated['per_page'] ?? 10,
                    'page'           => $validated['page'] ?? 1,
                ]
            );

            return Response::text(
                $businesses->map(function ($item) {
                    return "id: " . $item['id']
                        . " name: " . $item['name']
                        . " description: " . $item['description'];
                })
            );
        } catch (ValidationException $e) {
            logger()->warning('ListBusinessesTool validation failed', ['errors' => $e->errors()]);

            return Response::text('Invalid input: ' . implode(' ', array_merge(...array_values($e->errors()))));
        } catch (\Throwable $e) {
            logger()->error('ListBusinessesTool failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return Response::text('Failed to retrieve businesses. Please try again later.');
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'q'              => $schema->string('Keyword to search by business name, description, branch city/name, or service name.'),
            'city'           => $schema->string('Filter businesses with an active branch in this city.'),
            'max_price'      => $schema->number('Filter businesses offering at least one service at or below this price.'),
            'max_duration'   => $schema->integer('Filter businesses offering at least one service within this duration in minutes.'),
            'location_types' => $schema->array('Filter by service location types (e.g. "online", "in_person").')->items($schema->string()),
            'per_page'       => $schema->integer('Number of results per page. Defaults to 10.'),
            'page'           => $schema->integer('Page number for pagination. Defaults to 1.'),
        ];
    }
}
