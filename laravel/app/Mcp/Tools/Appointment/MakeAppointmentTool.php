<?php

namespace App\Mcp\Tools\Appointment;

use App\Application\Appointment\DTO\CreateAppointmentDTO;
use App\Application\Appointment\UseCases\CreateAppointment;
use Illuminate\Contracts\JsonSchema\JsonSchema;
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
        This tool creates (books) a new appointment for the authenticated user.

        ## When to use
        Use this tool when a user wants to book, schedule, or make an appointment for a specific
        service at a specific asset on a given date and time slot.

       Use this tool only when you have enough context, the context is best to receive using other tools
        - ListServicesTool
        - ListAssetsTool
        - ListBusinessesTool
        - ListBranchesTool

        ## Required parameters
        - service_id: The ID of the service to book. If you don't have context about service, ask user which service to book.
        - asset_id: The ID of the asset (e.g. room, chair, court) to book. If you don't have context about asset, ask user which asset to use.
        - date: The date of the appointment in YYYY-MM-DD format. If you don't have date provided ask at what date user wants the appointment to be.
        - start_at: The desired start time in HH:MM format (e.g. "09:00"). Must be a slot. If you don't have time provided ask at what time user wants the appointment to be.

        ## Validation
        If the requested slot is no longer available (e.g. taken by another user between
        slot-check and booking), the tool will return an error asking the user to pick another time.

        ## Example use case
        User says: "Book a haircut at  for me tomorrow at 10am."
        → Resolve service_id for haircut. If you don't have the ID use other tools to get more info about business or service or ask user to provide more context.
        → Resolve asset_id. Use tool to provide more info about assets or ask user for more context.
        → Resolve date - Use date that is tomorrow
        → start_at - Use date provided - 10:00

    MARKDOWN;

    public function __construct(
        private readonly CreateAppointment $createAppointment
    ) {}

    public function handle(Request $request): Response
    {
        $user =  Auth::user();

        logger()->debug('User: ', ['$user' => $user]);

        if (! $user) {
            return Response::text('Unauthorized: you must be logged in to book an appointment.');
        }

        $validated = $request->validate([
            'service_id' => 'nullable|integer',
            'asset_id'   => 'nullable|integer',
            'date'       => 'nullable|date_format:Y-m-d',
            'start_at'   => 'nullable|date_format:H:i',
        ]);

        if (!$validated['service_id']) {
            return Response::text('service_id argument is missing. Ask which service the user wants to use.');
        }

        if (!$validated['asset_id']) {
            return Response::text('asset_id argument is missing. Ask which asset the user wants to use.');
        }

        if (!$validated['date']) {
            return Response::text('date argument is missing. Ask at what date user wants the appointment to be.');
        }

        if (!$validated['start_at']) {
            return Response::text('start_at argument is missing. Ask at what time user wants the appointment to be.');
        }

        $appointment = $this->createAppointment->execute(
            new CreateAppointmentDTO(
                assetId: $validated['asset_id'],
                serviceId: $validated['service_id'],
                date: $validated['date'],
                startAt: $validated['start_at'],
            ),
            $user->id,
            $user
        );

        return Response::text(
            "Appointment booked successfully. " .
            "date: {$appointment->date} " .
            "start_at: {$appointment->start_at} "
        );
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'service_id' => $schema->integer()->description('The ID of the service to book.'),
            'asset_id' => $schema->integer()->description('The ID of the asset to book.'),
            'date' => $schema->string()->description('The appointment date in YYYY-MM-DD format.'),
            'start_at' => $schema->string()->description('The start time slot in HH:MM format (e.g. "09:00").'),
        ];
    }
}
