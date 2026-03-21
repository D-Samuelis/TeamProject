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
class GetAppointmentTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Retrieves a single appointment by ID for the currently authenticated user.

        Only returns the appointment if it belongs to the logged-in user.
        Returns: nothing yet

    MARKDOWN;

    public function handle(Request $request): Response
    {
        $user = Auth::user();

        if (!$user) {
            return Response::text('Unauthenticated. No user is currently logged in.');
        }

        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        return Response::text("No response.");

    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The unique identifier of the appointment.'),
        ];
    }
}
