<?php

use App\Mcp\Servers\McpServer;
use Laravel\Mcp\Facades\Mcp;

//TODO add auth
Mcp::web('/mcp', McpServer::class);
