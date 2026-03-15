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
        Returns: id, status, date, start_at, duration, end_time, and related service and asset details.

        Traversal:
        - Use GetServiceTool with service_id to get full service details.
        - Use GetAssetTool with asset_id to get full asset details.
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

        $appointment = Appointment::with([
            'service:id,name,description,price,duration_minutes,location_type',
            'asset:id,name,description',
        ])
            ->where('user_id', $user->id)
            ->find($validated['id']);

        if (!$appointment) {
            return Response::text("No appointment found with ID {$validated['id']} for the current user.");
        }

        $data = $appointment->toArray();
        $data['end_time'] = $appointment->endTime();

        return Response::text(json_encode($data));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The unique identifier of the appointment.'),
        ];
    }
}
