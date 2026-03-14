<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CreateAppointmentTool;
use App\Mcp\Tools\SearchBranchTool;
use App\Mcp\Tools\SearchServiceTool;
use Laravel\Mcp\Server;

class McpServer extends Server
{
    protected string $name = 'Appointment Server';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        This is the Mcp Server for BEXORA.

        It provides functionalities related to managing appointments for users.

        Users can send a message to this server to book/create, change/update, or cancel/delete appointments.
        User can also ask for information about available services, businesses that own the services, their branches and assets to help them make informed decisions when booking appointments.

        The server will respond with the appropriate information based on the user's request.
    MARKDOWN;

    protected array $tools = [
        CreateAppointmentTool::class,
        SearchServiceTool::class,
        SearchBranchTool::class,
    ];

    protected array $resources = [
    ];

    protected array $prompts = [
    ];
}
