<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class CreateAppointmentTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Use this tool to create a new appointment.

        Currently no appointment will actually be created, but you should respond with a success message if the input is valid.

        The tool accepts description and time as input parameters.
        If they are not provided or do not meet the validation criteria, ask the user to provide more information.

        todo better description
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'description' => 'required|string|min:10|max:50',
            'time' => 'required|string'
        ]);

        // TODO - logic

        $description = $request->string('description')->value();
        $time = $request->string('time')->value();

        return Response::text('Appointment with description "' . $description . '" at time "' . $time . '" has been successfully created.');
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'description' => $schema
                ->string()
                ->min(10)
                ->max(50)
                ->required()
                ->description(<<<'MARKDOWN'
                    A description or name of the service for which the appointment is being created.

                    This can be a specific service offered by the business (e.g., "haircut", "dental check-up")
                    or a general description of the appointment (e.g., "consultation with Dr. Smith")
                    or concrete name of the service (e.g. "Smoking Bob's Barbeque").
                MARKDOWN),
            'time' => $schema
                ->string()
                ->required()
                ->description(<<<'MARKDOWN'
                    The desired date or time for the appointment.

                    This can be a specific date and time (e.g., "2024-07-01 14:00")
                    or a relative time (e.g., "next Monday at 3 PM")
                    or vague description (e.g. "immediately").
                MARKDOWN),
        ];
    }
}
