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
        Lists appointments for the currently authenticated user. Returns paginated results (50 per page).

        Only returns appointments belonging to the logged-in user — never returns other users' appointments.

        Filters:
        - service_id: filter appointments for a specific service
        - asset_id: filter appointments involving a specific asset
        - date_from: filter appointments on or after this date (format: YYYY-MM-DD)
        - date_to: filter appointments on or before this date (format: YYYY-MM-DD)
        - cursor: last seen ID for pagination — use next_cursor from previous response

        Typical use: call with no filters to get all upcoming appointments for the current user.
        Or call with date_from and date_to to get appointments in a specific date range.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $user = Auth::user();

        if (!$user) {
            return Response::text('Unauthenticated. No user is currently logged in.');
        }

        $validated = $request->validate([
            'service_id' => 'nullable|integer',
            'asset_id'   => 'nullable|integer',
            'date_from'  => 'nullable|date_format:Y-m-d',
            'date_to'    => 'nullable|date_format:Y-m-d',
            'cursor'     => 'nullable|integer',
        ]);

        $appointments = Appointment::query()
            ->where('user_id', $user->id)
            ->when(!empty($validated['service_id']), fn($q) =>
            $q->where('service_id', $validated['service_id'])
            )
            ->when(!empty($validated['asset_id']), fn($q) =>
            $q->where('asset_id', $validated['asset_id'])
            )
            ->when(!empty($validated['date_from']), fn($q) =>
            $q->whereDate('date', '>=', $validated['date_from'])
            )
            ->when(!empty($validated['date_to']), fn($q) =>
            $q->whereDate('date', '<=', $validated['date_to'])
            )
            ->when(!empty($validated['cursor']), fn($q) =>
            $q->where('id', '>', $validated['cursor'])
            )
            ->with([
                'service:id,name,price,duration_minutes',
                'asset:id,name',
            ])
            ->orderBy('date')
            ->orderBy('start_at')
            ->limit(50)
            ->get()
            ->map(function ($appointment) {
                $data = $appointment->toArray();
                $data['end_time'] = $appointment->endTime();
                return $data;
            });

        if ($appointments->isEmpty()) {
            return Response::text('No appointments found for the current user.');
        }

        $result = [
            'items'       => $appointments,
            'next_cursor' => $appointments->count() === 50 ? $appointments->last()['id'] : null,
        ];

        return Response::text(json_encode($result));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'service_id' => $schema->integer()->description('Filter appointments by service ID.'),
            'asset_id'   => $schema->integer()->description('Filter appointments by asset ID.'),
            'date_from'  => $schema->string()->description('Start of date range (YYYY-MM-DD).'),
            'date_to'    => $schema->string()->description('End of date range (YYYY-MM-DD).'),
            'cursor'     => $schema->integer()->description('Last seen ID for pagination.'),
        ];
    }
}
