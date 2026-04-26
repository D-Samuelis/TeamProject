<?php

namespace App\Mcp\Tools\Appointment;

use App\Application\Appointment\DTO\CreateAppointmentDTO;
use App\Application\Appointment\UseCases\CreateAppointment;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\ValidationException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Illuminate\Support\Facades\Auth;

#[IsReadOnly(false)]
#[IsDestructive(false)]
#[IsOpenWorld(false)]
#[IsIdempotent(false)]
class MakeAppointmentTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        This tool creates a new appointment for the current user.

        Appointments are bookings made by users for a specific service at a specific asset
        on a given date and time slot.

        ## When to use
        Use this tool only when you have all required parameters. Gather context first using:
        - `ListBusinessesTool` — to find the business
        - `ListBranchesTool` — to find the branch
        - `ListServicesTool` — to find the service and its ID
        - `ListAssetsTool` (with `service_id`, `from`, `to`) — to find an asset and its available slots

        Do not guess IDs. Do not proceed without a confirmed available slot from `ListAssetsTool`.

        ## Required parameters
        - `asset_id`: ID of the asset to book.
        - `service_id`: ID of the service to book.
        - `date`: The date of the appointment (e.g. "2025-06-01").
        - `start_at`: The start time of the slot (e.g. "09:00"). Must be a slot returned by ListAssetsTool.

        ## Validation
        - The slot must be available — the use case will reject double-bookings.
        - The asset must be active.
        - `start_at` must match an available slot exactly for the given `date`.

    MARKDOWN;

    public function __construct(
        private readonly CreateAppointment $createAppointment,
    ) {}

    public function handle(Request $request): Response
    {
        $user = Auth::user();

        if (! $user) {
            return Response::text('Unauthorized: you must be logged in to book an appointment.');
        }

        try {
            $validated = $request->validate([
                'asset_id'   => 'required|integer',
                'service_id' => 'required|integer',
                'date'       => 'required|date_format:Y-m-d',
                'start_at'   => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
            ]);

            $dto = new CreateAppointmentDTO(
                assetId:   $validated['asset_id'],
                serviceId: $validated['service_id'],
                date:      $validated['date'],
                startAt:   $validated['start_at'],
            );

            $appointment = $this->createAppointment->execute($dto, $user->id);

            return Response::text(
                "appointment id: {$appointment->id}"
                . ", service id: {$appointment->service_id}"
                . ", asset id: {$appointment->asset_id}"
                . ", date: {$appointment->date}"
                . ", time: {$appointment->start_at}"
                . ", status: {$appointment->status}"
            );

        } catch (ValidationException $e) {
            logger()->warning('MakeAppointmentTool validation failed', ['errors' => $e->errors()]);

            return Response::text('Booking failed: ' . implode(' ', array_merge(...array_values($e->errors()))));
        } catch (\Throwable $e) {
            logger()->error('MakeAppointmentTool failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return Response::text('Failed to book appointment. Please try again later.');
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'asset_id'   => $schema->integer('The ID of the asset to book (e.g. a therapist or room).'),
            'service_id' => $schema->integer('The ID of the service to book.'),
            'date'       => $schema->string('The date of the appointment in Y-m-d format (e.g. "2025-06-01").'),
            'start_at'   => $schema->string('The start time of the slot in HH:MM format (e.g. "09:00"). Must match an available slot.'),
        ];
    }
}
