<?php

namespace App\Mcp\Servers;

use App\Mcp\Resources\AvailableBookingResource;
use App\Mcp\Resources\BookedAppointmentsResource;
use App\Mcp\Resources\PreviousAppointmentsResource;
use App\Mcp\Tools\CreateAppointmentTool;
use App\Mcp\Tools\GetBookedAppointmentsTool;
use Laravel\Mcp\Server;

class AppointmentServer extends Server
{
    protected string $name = 'Appointment Server';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        This is the Appointment Server.

        It provides functionalities related to managing appointments.

        Users can send a message to this server to book/create, change/update, or cancel/delete appointments.
        The server will respond with the appropriate information based on the user's request.
    MARKDOWN;

    protected array $tools = [
        CreateAppointmentTool::class,
        GetBookedAppointmentsTool::class,
    ];

    protected array $resources = [
        PreviousAppointmentsResource::class,
        BookedAppointmentsResource::class,
        AvailableBookingResource::class,
    ];

    protected array $prompts = [
    ];
}
