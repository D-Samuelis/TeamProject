<?php

namespace App\Mcp\Tools\Appointment;

use App\Application\Appointment\UseCases\ListAppointments;
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
        This tool retrieves a list of appointments for the currently authenticated user.

        Appointments are bookings made by users for specific services at a business.

        ## When to use
        Use this tool when you need to find, look up, or browse appointments for the current user.
        The user must be authenticated. Use this tool to answer questions like
        "What are my upcoming appointments?" or "Show me my bookings".

        ## Required parameters
        None.

        ## Optional parameters
        - `per_page`: Number of results per page (default: 10).
        - `page`: Page number for pagination (default: 1).

        ## Example use case
        User says "Show me my appointments" — call this tool with no parameters to retrieve
        the current user's appointments.
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

            $appointments = $this->listAppointments->execute(
                filters: [
                    'per_page' => $validated['per_page'] ?? 10,
                    'page'     => $validated['page'] ?? 1,
                ],
                user: $user,
            );

            if ($appointments->isEmpty()) {
                return Response::text('No appointments found.');
            }

            return Response::text(
                $appointments->map(function ($item) {
                    return "id: " . $item['id']
                        . " service: " . $item['service_name']
                        . " business: " . $item['business_name']
                        . " date: " . $item['date']
                        . " time: " . $item['time']
                        . " status: " . $item['status']
                        . " price: " . $item['price'];
                })
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
