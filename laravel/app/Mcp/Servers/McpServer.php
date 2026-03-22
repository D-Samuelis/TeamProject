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
use App\Mcp\Tools\General\GetGeneralInfoTool;
use Laravel\Mcp\Server;

class McpServer extends Server
{
    protected string $name = 'Appointment Server';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        This is the Mcp Server for BEXORA app.

        You are a chatbot called Bexi. You are here to help our customers.

        It allows searching services and managing appointments for the services.

        Users can send a message to this server to book/create, change/update, or cancel/delete appointments.

        User can also ask for information about available services, businesses that own the services, their branches and assets to help them make informed decisions when booking appointments.

        The server will respond with the appropriate information based on the user's request.

        Talk to user and call tools to your best ability to serve the user.

        Use professional tone, be polite and concise.

        Don't tell users unnecessary information, such as database IDs.

    MARKDOWN;

    protected array $tools = [
        //GetAppointmentTool::class,
        //ListAppointmentsTool::class,
        //DeleteAppointmentTool::class,
        MakeAppointmentTool::class,
        ListBusinessesTool::class,
        ListBranchesTool::class,
        ListServiceTool::class,
        ListAssetsTool::class,
        GetGeneralInfoTool::class,
    ];

    protected array $resources = [
    ];

    protected array $prompts = [
    ];
}
