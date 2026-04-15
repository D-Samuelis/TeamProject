<?php

namespace App\Mcp\Tools\Appointment;

use App\Application\Appointment\DTO\GetSlotsDTO;
use App\Application\Appointment\UseCases\GetAvailableSlots;
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
class GetAvailableSlotsTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        This tool retrieves available time slots for booking a specific service on a given asset (e.g. a staff member, room, or resource).

        ## When to use
        Use this tool when a user wants to book a service and you need to show them
        what times are available. Always call ListServicesTool first to obtain a valid
        `service_id`, and ensure you have an `asset_id` before calling this tool.

        ## Required parameters
        - `asset_id`: The ID of the asset (e.g. staff member or resource) to check availability for.
        - `service_id`: The ID of the service being booked.

        ## Optional parameters
        - `from`: Start date of the range to check (format: YYYY-MM-DD). Defaults to today.
        - `to`: End date of the range to check (format: YYYY-MM-DD). Defaults to 6 days after `from` (a full week).

        ## Response format
        Returns a map of dates to available time slots, e.g.:
        2025-07-01: 09:00, 09:30, 10:00
        2025-07-02: 14:00, 14:30
        Dates with no available slots are omitted from the response.

        ## Example use case
        User says "I want to book a massage next Monday and Tuesday" — call this tool with
        `asset_id` and `service_id` for the massage, `from: "2025-07-07"`, `to: "2025-07-08"`
        to retrieve available slots for those days.
    MARKDOWN;

    public function __construct(
        private readonly GetAvailableSlots $getAvailableSlots,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'asset_id'   => 'required|integer|min:1',
                'service_id' => 'required|integer|min:1',
                'from'       => 'nullable|date_format:Y-m-d',
                'to'         => 'nullable|date_format:Y-m-d|after_or_equal:from',
            ]);

            $from = Carbon::parse($validated['from'] ?? now()->toDateString());
            $to   = Carbon::parse($validated['to']   ?? $from->copy()->addDays(6)->toDateString());


            $dto = new GetSlotsDTO(
                assetId:   (int) $validated['asset_id'],
                serviceId: (int) $validated['service_id'],
                from:      $from,
                to:        $to,
            );

            $slots = $this->getAvailableSlots->execute($dto, $request->user());

            $nonEmpty = array_filter($slots, fn(array $times) => !empty($times));

            if (empty($nonEmpty)) {
                return Response::text('No available slots found for the given date range.');
            }

            $lines = array_map(
                fn(string $date, array $times) => $date . ': ' . implode(', ', $times),
                array_keys($nonEmpty),
                $nonEmpty,
            );

            return Response::text(implode("\n", $lines));

        } catch (ValidationException $e) {
            logger()->warning('GetAvailableSlotsTool validation failed', ['errors' => $e->errors()]);

            return Response::text('Invalid input: ' . implode(' ', array_merge(...array_values($e->errors()))));
        } catch (\Throwable $e) {
            logger()->error('GetAvailableSlotsTool failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return Response::text('Failed to retrieve available slots. Please try again later.');
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'asset_id'   => $schema->integer('The ID of the asset to check availability for.'),
            'service_id' => $schema->integer('The ID of the service being booked.'),
            'from'       => $schema->string('Start date of the range to check (YYYY-MM-DD).'),
            'to'         => $schema->string('End date of the range to check (YYYY-MM-DD).'),
        ];
    }
}
