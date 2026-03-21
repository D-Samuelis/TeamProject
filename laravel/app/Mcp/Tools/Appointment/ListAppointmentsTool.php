<?php

namespace App\Mcp\Tools\Appointment;

use App\Models\Business\Appointment;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly(true)]
#[IsDestructive(false)]
#[IsOpenWorld(false)]
#[IsIdempotent(true)]
class ListAppointmentsTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        This tool retrieves a list of appointments from the system based on a search query.

        ## When to use
        Use this tool when you need to find, look up, or browse appointments for certain user.
        User needs to be authorized to request the appointments.

        Appointments are requests of users for services.

        ## Required parameters
        None yet.

        ## Optional parameters
        None yet.

        ## Example use case

    MARKDOWN;

    public function __construct(
    ) {}

    public function handle(Request $request): Response
    {
        return Response::text('Nothing for now.');
    }

    public function schema(JsonSchema $schema): array
    {
        return [

        ];
    }
}
