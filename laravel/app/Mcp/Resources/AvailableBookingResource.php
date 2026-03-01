<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class AvailableBookingResource extends Resource
{
    protected string $description = <<<'MARKDOWN'
        This resource provides information about available bookings for the user.

        It can be used to retrieve list of available services and their branches and assets.
    MARKDOWN;

    /**
     * Handle the resource request.
     */
    public function handle(Request $request): Response
    {
        //

        return Response::text('Available booking options');
    }
}
