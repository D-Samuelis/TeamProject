<?php

return [
    'url' => env('MCP_SERVER', 'http://127.0.0.1:8000/mcp'),
    'ollama_url' => env('MCP_OLLAMA_URL', 'http://localhost:11434/api/chat'),
    'ollama_model' => env('MCP_OLLAMA_MODEL', 'llama3.2:1b'),
    'client_url' => env('MCP_SERVICE_URL', 'http://127.0.0.1:8001'),
];
