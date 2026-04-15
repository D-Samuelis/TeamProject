<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\Appointment\GetAvailableSlotsTool;
use App\Mcp\Tools\Appointment\ListAppointmentsTool;
use App\Mcp\Tools\Appointment\MakeAppointmentTool;
use App\Mcp\Tools\Asset\ListAssetsTool;
use App\Mcp\Tools\Branch\ListBranchesTool;
use App\Mcp\Tools\Business\ListBusinessesTool;
use App\Mcp\Tools\Service\ListServicesTool;
use Laravel\Mcp\Server;

class McpServer extends Server
{
    protected string $name = 'Bexora MCP Server';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        ## General Information
            You are a chatbot called Bexi. You are here to help our customers.
            This is the Mcp Server for BEXORA application.
            BEXORA is a booking application that allows customers to book appointments with various businesses.
            The MCP Server allows you to access the tools that are available in the BEXORA application.
            You can use these tools to help our customers find information about businesses, branches, services, assets, find available slots and make appointments.

        ## Tools
            You have access to the following tools:
            - ListAppointmentsTool: This tool allows you to list all appointments for a customer.
            - GetAvailableSlotsTool: This tool allows you to get available slots for a service at a branch.
            - MakeAppointmentTool: This tool allows you to make an appointment for a customer.
            - ListBusinessesTool: This tool allows you to search businesses.
            - ListBranchesTool: This tool allows you to search branches.
            - ListServicesTool: This tool allows you to search services.
            - ListAssetsTool: This tool allows you to search assets.

        ## Instructions
            - Always use the tools when you need to access information about businesses, branches, services, assets, available slots or appointments.
            - Do not make up information. If you don't know the answer, use the tools to find the answer.
            - Be helpful and provide accurate information to our customers.

    MARKDOWN;

    protected array $tools = [
        ListAppointmentsTool::class,
        GetAvailableSlotsTool::class,
        MakeAppointmentTool::class,
        ListBusinessesTool::class,
        ListBranchesTool::class,
        ListServicesTool::class,
        ListAssetsTool::class,
    ];

    protected array $resources = [
    ];

    protected array $prompts = [
    ];
}
