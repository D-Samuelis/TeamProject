<?php

use App\Mcp\Servers\AppointmentServer;
use Laravel\Mcp\Facades\Mcp;

// TODO - add auth using Laravel Sanctum
Mcp::web('/mcp/appointment', AppointmentServer::class);
