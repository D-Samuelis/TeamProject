<?php

namespace App\Mcp\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class McpClient
{
    private array $servers;       // ['name' => 'url']
    private string $ollamaUrl;
    private string $ollamaModel;
    private array $sessions = []; // ['serverUrl' => 'sessionId']

    public function __construct()
    {
        $this->servers = config('mcp.servers', [
            'appointment' => 'http://localhost:8080/mcp/appointment',
        ]);
        $this->ollamaUrl   = config('mcp.ollama_url', 'http://localhost:11434/api/chat');
        $this->ollamaModel = config('mcp.ollama_model', 'llama3.2:1b');
    }

    public function chat(string $userMessage): string
    {
        [$ollamaTools, $toolServerMap] = $this->initializeAllServers();

        $messages = [
            [
                'role'    => 'system',
                'content' => 'You are a helpful assistant. Use available tools to complete the user\'s request. Today is ' . now()->toDateTimeString(),
            ],
            ['role' => 'user', 'content' => $userMessage],
        ];

        $reply = $this->callOllama($messages, $ollamaTools)['message'] ?? null;

        while (!empty($reply['tool_calls'])) {
            foreach ($reply['tool_calls'] as $toolCall) {
                $prefixedName = $toolCall['function']['name'];
                $toolArgs     = $toolCall['function']['arguments'] ?? [];

                if (is_string($toolArgs)) {
                    $toolArgs = json_decode($toolArgs, true) ?? [];
                }

                // Resolve which server owns this tool
                [$serverUrl, $realToolName] = $this->resolveToolServer($prefixedName, $toolServerMap);

                if (!$serverUrl) {
                    $resultText = "Error: unknown tool '{$prefixedName}'";
                    $this->print("⚠  Unknown tool: {$prefixedName}");
                } else {
                    $this->print("🔧 Using tool: {$prefixedName}" . (!empty($toolArgs) ? ' ' . json_encode($toolArgs) : ''));
                    $resultText = $this->callTool($serverUrl, $realToolName, $toolArgs);
                    $this->print("✅ Tool done:  {$prefixedName}");
                }

                $messages[] = ['role' => 'assistant', 'content' => null, 'tool_calls' => [$toolCall]];
                $messages[] = ['role' => 'tool', 'content' => $resultText];
            }

            $reply = $this->callOllama($messages, $ollamaTools)['message'] ?? null;
        }

        return $reply['content'] ?? 'No response';
    }

    // -------------------------------------------------------------------------
    // Output
    // -------------------------------------------------------------------------

    private function print(string $message): void
    {
        $timestamp = now()->format('H:i:s');
        $line = "[{$timestamp}] {$message}" . PHP_EOL;

        // Works in CLI (artisan commands) and writes to Laravel log
        if (app()->runningInConsole()) {
            echo $line;
        }

        Log::info(strip_tags($message));
    }

    // -------------------------------------------------------------------------
    // Server initialisation
    // -------------------------------------------------------------------------

    /**
     * Initialise every configured MCP server, aggregate their tools, and return:
     *   - $ollamaTools    : tool definitions formatted for Ollama
     *   - $toolServerMap  : ['prefixed_tool_name' => ['url' => ..., 'real' => ...]]
     */
    private function initializeAllServers(): array
    {
        $ollamaTools   = [];
        $toolServerMap = [];

        foreach ($this->servers as $serverName => $serverUrl) {
            try {
                $this->print("🔌 Connecting to server: {$serverName} ({$serverUrl})");
                $tools = $this->initializeServer($serverName, $serverUrl);
                $this->print("✅ Connected: {$serverName} — " . count($tools) . " tool(s) loaded");
            } catch (\Throwable $e) {
                $this->print("❌ Failed to init server '{$serverName}': " . $e->getMessage());
                Log::warning("MCP: failed to init server '{$serverName}': " . $e->getMessage());
                continue;
            }

            foreach ($tools as $tool) {
                $prefixedName = $serverName . '__' . $tool['name'];

                // Guard against duplicate prefixed names across servers
                if (isset($toolServerMap[$prefixedName])) {
                    Log::warning("MCP: tool name collision for '{$prefixedName}', skipping duplicate.");
                    continue;
                }

                $toolServerMap[$prefixedName] = [
                    'url'  => $serverUrl,
                    'real' => $tool['name'],
                ];

                $ollamaTools[] = [
                    'type'     => 'function',
                    'function' => [
                        'name'        => $prefixedName,
                        'description' => "[{$serverName}] " . ($tool['description'] ?? ''),
                        'parameters'  => $tool['inputSchema'] ?? ['type' => 'object', 'properties' => []],
                    ],
                ];
            }
        }

        return [$ollamaTools, $toolServerMap];
    }

    /**
     * Run the MCP handshake for one server and return its tool list.
     */
    private function initializeServer(string $serverName, string $serverUrl): array
    {
        $initResponse = $this->mcpRequest($serverUrl, 'initialize', [
            'protocolVersion' => '2024-11-05',
            'capabilities'    => [],
            'clientInfo'      => ['name' => 'OllamaClient', 'version' => '1.0'],
        ]);

        if (isset($initResponse['error'])) {
            throw new \RuntimeException($initResponse['error']['message']);
        }

        // Store session per server
        if ($sessionId = $initResponse['_sessionId'] ?? null) {
            $this->sessions[$serverUrl] = $sessionId;
        }

        $this->mcpNotify($serverUrl, 'notifications/initialized');

        return $this->mcpRequest($serverUrl, 'tools/list')['result']['tools'] ?? [];
    }

    private function resolveToolServer(string $prefixedName, array $toolServerMap): array
    {
        if (!isset($toolServerMap[$prefixedName])) {
            return [null, null];
        }

        return [
            $toolServerMap[$prefixedName]['url'],
            $toolServerMap[$prefixedName]['real'],
        ];
    }

    private function callTool(string $serverUrl, string $toolName, array $toolArgs): string
    {
        $toolResult = $this->mcpRequest($serverUrl, 'tools/call', [
            'name'      => $toolName,
            'arguments' => $toolArgs,
        ]);

        $text = '';
        foreach ($toolResult['result']['content'] ?? [] as $content) {
            $text .= $content['text'] ?? json_encode($content);
        }

        return $text;
    }

    // -------------------------------------------------------------------------
    // HTTP helpers
    // -------------------------------------------------------------------------

    private function mcpRequest(string $serverUrl, string $method, array $params = []): array
    {
        return Http::withHeaders($this->mcpHeaders($serverUrl))
            ->timeout(30)
            ->post($serverUrl, [
                'jsonrpc' => '2.0',
                'id'      => uniqid(),
                'method'  => $method,
                'params'  => $params,
            ])->json() ?? [];
    }

    private function mcpNotify(string $serverUrl, string $method, array $params = []): void
    {
        Http::withHeaders($this->mcpHeaders($serverUrl))
            ->timeout(10)
            ->post($serverUrl, [
                'jsonrpc' => '2.0',
                'method'  => $method,
                'params'  => $params,
            ]);
    }

    private function mcpHeaders(string $serverUrl): array
    {
        $headers = ['Content-Type' => 'application/json', 'Accept' => 'application/json'];

        if (isset($this->sessions[$serverUrl])) {
            $headers['Mcp-Session-Id'] = $this->sessions[$serverUrl];
        }

        return $headers;
    }

    private function callOllama(array $messages, array $tools): array
    {
        return Http::timeout(60)->post($this->ollamaUrl, [
            'model'    => $this->ollamaModel,
            'messages' => $messages,
            'tools'    => $tools,
            'stream'   => false,
        ])->json() ?? [];
    }
}
