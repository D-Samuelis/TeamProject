<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\Appointment\ListAppointmentsTool;
use App\Mcp\Tools\Appointment\MakeAppointmentTool;
use App\Mcp\Tools\Asset\ListAssetsTool;
use App\Mcp\Tools\Branch\ListBranchesTool;
use App\Mcp\Tools\Business\ListBusinessesTool;
use App\Mcp\Tools\General\GetCurrentDateTool;
use App\Mcp\Tools\Service\ListServicesTool;
use Laravel\Mcp\Server;

class McpServer extends Server
{
    protected string $name = 'Bexora MCP Server';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        ## General Information
            This is the Mcp Server for BEXORA application which allows you to access tools that are available in the BEXORA application.
            Use these tools to help our customers find information about businesses, branches, services, assets, find available slots and make appointments.

        ## Tools
            You have access to the following tools:
            - GetCurrentDateTool: Get the current date and time.
            - ListAppointmentsTool: List booked appointments for current user.
            - MakeAppointmentTool: Make an appointment for a user.
            - ListBusinessesTool: Browse businesses.
            - ListBranchesTool: Browse branches.
            - ListServicesTool: Browse services.
            - ListAssetsTool:Browse assets.

    MARKDOWN;

    protected array $tools = [
        ListAppointmentsTool::class,
        MakeAppointmentTool::class,
        ListBusinessesTool::class,
        ListBranchesTool::class,
        ListServicesTool::class,
        ListAssetsTool::class,
        GetCurrentDateTool::class,
    ];

    protected array $resources = [
    ];

    protected array $prompts = [
    ];
}
