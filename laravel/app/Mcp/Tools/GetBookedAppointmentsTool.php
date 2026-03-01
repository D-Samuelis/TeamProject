<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetBookedAppointmentsTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Use this tool to retrieve information of the user's currently booked appointments.

        This tool currently returns mocked data.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $appointments = [
            [
                'id' => 1,
                'title' => 'Dental Checkup',
                'date' => '2026-03-05',
                'time' => '10:00',
                'duration_minutes' => 60,
                'location' => 'City Dental Clinic',
                'status' => 'confirmed',
            ],
            [
                'id' => 2,
                'title' => 'Project Meeting',
                'date' => '2026-03-10',
                'time' => '14:30',
                'duration_minutes' => 90,
                'location' => 'Office - Room 204',
                'status' => 'confirmed',
            ],
        ];

        return Response::json([
            'success' => true,
            'appointments' => $appointments,
        ]);


    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'message' => $schema
                ->string()
                ->description(<<<'MARKDOWN'
                    User tells the server to retrieve the user's currently booked appointments.
                MARKDOWN),
        ];
    }
}
