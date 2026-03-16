<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\Appointment\DeleteAppointmentTool;
use App\Mcp\Tools\Appointment\GetAppointmentTool;
use App\Mcp\Tools\Appointment\ListAppointmentsTool;
use App\Mcp\Tools\Appointment\MakeAppointmentTool;
use App\Mcp\Tools\Asset\ListAssetsTool;
use App\Mcp\Tools\Branch\ListBranchesTool;
use App\Mcp\Tools\Business\ListBusinessesTool;
use App\Mcp\Tools\Service\ListServiceTool;
use Laravel\Mcp\Server;

class McpServer extends Server
{
    protected string $name = 'Appointment Server';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        This is the Mcp Server for BEXORA app.

        It allows searching services and managing appointments for the services.

        Users can send a message to this server to book/create, change/update, or cancel/delete appointments.

        User can also ask for information about available services, businesses that own the services, their branches and assets to help them make informed decisions when booking appointments.

        The server will respond with the appropriate information based on the user's request.

        RESPOND TO USER WITH AS LITTLE WORDS AS POSSIBLE.
        RESPOND IN HUMAN FORM.
        DON'T SAY UNNECESSARY INFO, MOST OF THE TIME ONLY THE NAME OF Service/Business/Branch/Asset IS ENOUGH.
    MARKDOWN;

    protected array $tools = [
        GetAppointmentTool::class,
        ListAppointmentsTool::class,
        DeleteAppointmentTool::class,
        MakeAppointmentTool::class,
        ListBusinessesTool::class,
        ListBranchesTool::class,
        ListServiceTool::class,
        ListAssetsTool::class,
    ];

    protected array $resources = [
    ];

    protected array $prompts = [
    ];
}
