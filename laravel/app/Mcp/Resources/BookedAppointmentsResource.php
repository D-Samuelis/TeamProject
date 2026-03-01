<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class BookedAppointmentsResource extends Resource
{
    protected string $description = <<<'MARKDOWN'
        Use this resource to retrieve information of the user's currently booked appointments.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        return Response::text('Booked appointments resource response');
    }
}
