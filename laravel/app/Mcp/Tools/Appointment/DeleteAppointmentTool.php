<?php

namespace App\Mcp\Tools\Appointment;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly(false)]
#[IsDestructive(true)]
#[IsOpenWorld(false)]
#[IsIdempotent(true)]
class DeleteAppointmentTool extends Tool
{

    protected string $description = <<<'MARKDOWN'
        Deletes appointment for authenticated user.

        Currently returns mock response
    MARKDOWN;


    public function handle(Request $request): Response
    {

        return Response::text('Appointment was deleted successfully');
    }


    public function schema(JsonSchema $schema): array
    {
        return [
        ];
    }
}
