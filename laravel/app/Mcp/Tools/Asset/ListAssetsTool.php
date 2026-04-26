<?php

namespace App\Mcp\Tools\Asset;

use App\Application\Asset\UseCases\ListAssets;
use App\Application\Appointment\UseCases\GetAvailableSlots;
use App\Application\Appointment\DTO\GetSlotsDTO;
use Carbon\Carbon;
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

        Assets may either represent a commodity (for example "Table", "Chair" or "Room"),
        or employees (for example name like "Josh" or role like "masseur").

        ## When to use
        Use this tool when you need to find, look up, or browse assets. Best to use this only when you have business_id.

        ## Required parameters
        - `business_id`

        ## Optional parameters
        - `per_page`: Number of results per page (default: 10).
        - `page`: Page number (default: 1).
        - `business_id`: The ID of the business to list assets for.
        - `service_id`: If provided along with `from` and `to`, available time slots will be included per asset.
        - `from`: Start date for availability check (e.g. "2025-06-01"). Requires `service_id` and `to`.
        - `to`: End date for availability check (e.g. "2025-06-07"). Requires `service_id` and `from`.

        ## Example use cases
        - User wants to browse assets for a business.
        - User wants to book a massage —  provide `business_id` and `service_id`, `from`, and `to` to see available slots per asset.

    MARKDOWN;

    public function __construct(
        private readonly ListAssets $listAssets,
        private readonly GetAvailableSlots $getAvailableSlots,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'business_id' => 'nullable|integer',
                'per_page'    => 'nullable|integer|min:1|max:100',
                'page'        => 'nullable|integer|min:1',
                'service_id'  => 'nullable|integer',
                'from'        => 'nullable|date|required_with:service_id,to',
                'to'          => 'nullable|date|required_with:service_id,from',
            ]);

            $assets = $this->listAssets->execute(
                filters: [
                    'target'      => 'asset',
                    'business_id' => $validated['business_id'] ?? null,
                    'per_page'    => $validated['per_page'] ?? 10,
                    'page'        => $validated['page'] ?? 1,
                ],
                user: null,
            );

            $serviceId = $validated['service_id'] ?? null;
            $from      = isset($validated['from']) ? Carbon::parse($validated['from']) : null;
            $to        = isset($validated['to']) ? Carbon::parse($validated['to']) : null;
            $withSlots = $serviceId && $from && $to;

            $lines = $assets->map(function ($item) use ($serviceId, $from, $to, $withSlots) {
                $line = "asset id: {$item['id']}"
                    . ", asset name: {$item['name']}"
                    . ", asset description: {$item['description']}";

                if ($withSlots) {
                    $dto   = new GetSlotsDTO(
                        assetId:   $item['id'],
                        serviceId: $serviceId,
                        from:      $from->copy(),
                        to:        $to->copy(),
                    );
                    $slots = $this->getAvailableSlots->execute($dto);

                    $slotSummary = collect($slots)
                        ->map(fn($times, $date) => $date . ': ' . (
                            empty($times) ? 'no slots' : implode(', ', $times)
                            ))
                        ->implode(' | ');

                    $line .= ", available slots: [{$slotSummary}]";
                }

                return $line;
            });

            return Response::text($lines->implode("\n"));

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
            'service_id'  => $schema->integer('The ID of the service to check availability for. Required together with from and to.'),
            'from'        => $schema->string('Start date for availability window (e.g. "2025-06-01"). Required with service_id and to.'),
            'to'          => $schema->string('End date for availability window (e.g. "2025-06-07"). Required with service_id and from.'),
            'per_page'    => $schema->integer('Number of results per page. Defaults to 10.'),
            'page'        => $schema->integer('Page number for pagination. Defaults to 1.'),
        ];
    }
}
