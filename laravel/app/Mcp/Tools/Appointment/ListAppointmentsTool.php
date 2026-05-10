<?php

namespace App\Mcp\Tools\Appointment;

use App\Application\Appointment\UseCases\ListAppointments;
use App\Application\DTO\AppointmentSearchDTO;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\ValidationException;
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
        This tool retrieves a list of appointments for the current user.

        Appointments are bookings made by users for specific services at a business.

        ## When to use
        Use this tool when you need to find, look up, or browse appointments for the current user.
        Use this tool to answer questions like "What are my upcoming appointments?" or "Show me my bookings".

        ## Required parameters
        None.

        ## Optional parameters
        - `per_page`: Number of results per page (default: 10).
        - `page`: Page number for pagination (default: 1).

        ## Example use case
        - User says "Show me my appointments".
        - Use this tool with no parameters to retrieve the current user's appointments.
    MARKDOWN;

    public function __construct(
        private readonly ListAppointments $listAppointments,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $validated = $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'page'     => 'nullable|integer|min:1',
            ]);

            $user = $request->user();

            if (!$user) {
                return Response::text('Unauthorized: You must be logged in to view appointments.');
            }

            $dto = AppointmentSearchDTO::fromArray([
                $validated['per_page'] ?? 10,
                $validated['page'] ?? 1
            ]);

            $appointments = $this->listAppointments->execute(
                dto: $dto,
                user: $user,
            )->getCollection();

            if ($appointments->isEmpty()) {
                return Response::text('No appointments found.');
            }

            return Response::text(
                $appointments->map(function ($appointment) {
                    $service  = $appointment->service?->name ?? 'Unknown service';
                    $business = $appointment->asset?->branch?->business?->name ?? 'Unknown business';
                    $branch   = $appointment->asset?->branch?->name ?? 'Unknown branch';
                    $asset    = $appointment->asset?->name ?? 'Unknown asset';
                    $time     = \Carbon\Carbon::parse($appointment->start_at)->format('H:i');

                    return "appointment id: {$appointment->id}"
                        . ", service: {$service}"
                        . ", business: {$business}"
                        . ", branch: {$branch}"
                        . ", asset: {$asset}"
                        . ", date: {$appointment->date}"
                        . ", time: {$time}"
                        . ", duration: {$appointment->duration} min"
                        . ", status: {$appointment->status}";
                })->implode("\n")
            );

        } catch (ValidationException $e) {
            logger()->warning('ListAppointmentsTool validation failed', ['errors' => $e->errors()]);

            return Response::text('Invalid input: ' . implode(' ', array_merge(...array_values($e->errors()))));
        } catch (\Throwable $e) {
            logger()->error('ListAppointmentsTool failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return Response::text('Failed to retrieve appointments. Please try again later.');
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'per_page' => $schema->integer('Number of results per page. Defaults to 10.'),
            'page'     => $schema->integer('Page number for pagination. Defaults to 1.'),
        ];
    }
}
