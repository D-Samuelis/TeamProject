<?php

use App\Mcp\Servers\WeatherServer;
use App\Mcp\Servers\TaskServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/weather', WeatherServer::class);
Mcp::web('/mcp/tasks', TaskServer::class);
    //->middleware(['auth:sanctum']);
