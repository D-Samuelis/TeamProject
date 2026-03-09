<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreateAppointmentTool;
use Laravel\Mcp\Server;

class McpServer extends Server
{
    protected string $name = 'Appointment Server';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        This is the Appointment Server.

        It provides functionalities related to managing appointments.

        Users can send a message to this server to book/create, change/update, or cancel/delete appointments.
        The server will respond with the appropriate information based on the user's request.

        Todo - better instructions
    MARKDOWN;

    protected array $tools = [
        CreateAppointmentTool::class,
    ];

    protected array $resources = [
    ];

    protected array $prompts = [
    ];
}
