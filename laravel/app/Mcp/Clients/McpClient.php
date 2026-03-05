<?php

namespace App\Mcp\Clients;

use Illuminate\Support\Facades\Http;

class McpClient
{
    private array $servers;
    private string $ollamaUrl;
    private string $ollamaModel;
    private array $sessions = [];

    public function __construct()
    {
        $this->servers = config('mcp.servers', [
            'appointment' => 'http://127.0.0.1:8000/mcp/appointment',
        ]);

        $this->ollamaUrl   = config('mcp.ollama_url', 'http://localhost:11434/api/chat');
        $this->ollamaModel = config('mcp.ollama_model', 'llama3.2:1b');
    }

    public function chat(string $userMessage): string
    {
        [$tools, $map] = $this->initializeServers();

        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant. Today is ' . now()],
            ['role' => 'user', 'content' => $userMessage],
        ];

        $reply = $this->ollama($messages, $tools)['message'] ?? [];

        while (!empty($reply['tool_calls'])) {

            foreach ($reply['tool_calls'] as $call) {

                $name = $call['function']['name'];
                $args = $call['function']['arguments'] ?? [];

                if (is_string($args)) {
                    $args = json_decode($args, true) ?? [];
                }

                [$server, $tool] = $map[$name] ?? [null, null];

                $result = $server
                    ? $this->callTool($server, $tool, $args)
                    : "Unknown tool {$name}";

                $messages[] = ['role' => 'assistant', 'tool_calls' => [$call]];
                $messages[] = ['role' => 'tool', 'content' => $result];
            }

            $reply = $this->ollama($messages, $tools)['message'] ?? [];
        }

        return $reply['content'] ?? '';
    }

    private function initializeServers(): array
    {
        $tools = [];
        $map   = [];

        foreach ($this->servers as $name => $url) {

            echo "Connecting to {$name}...\n";

            $init = $this->rpc($url, 'initialize', [
                'protocolVersion' => '2024-11-05',
                'capabilities' => [],
                'clientInfo' => ['name' => 'LaravelMCP', 'version' => '1.0']
            ]);

            if ($sid = $init['_sessionId'] ?? null) {
                $this->sessions[$url] = $sid;
            }

            $this->rpc($url, 'notifications/initialized');

            $serverTools = $this->rpc($url, 'tools/list')['result']['tools'] ?? [];

            foreach ($serverTools as $tool) {

                $prefixed = "{$name}__{$tool['name']}";

                $map[$prefixed] = [$url, $tool['name']];

                $tools[] = [
                    'type' => 'function',
                    'function' => [
                        'name' => $prefixed,
                        'description' => "[{$name}] " . ($tool['description'] ?? ''),
                        'parameters' => $tool['inputSchema'] ?? ['type'=>'object','properties'=>[]],
                    ]
                ];
            }

            echo count($tools) . " tools available\n\n";
        }

        return [$tools, $map];
    }

    private function callTool(string $url, string $tool, array $args): string
    {
        $res = $this->rpc($url, 'tools/call', [
            'name' => $tool,
            'arguments' => $args
        ]);

        return collect($res['result']['content'] ?? [])
            ->pluck('text')
            ->implode('');
    }

    private function rpc(string $url, string $method, array $params = []): array
    {
        return Http::withHeaders($this->headers($url))
            ->post($url, [
                'jsonrpc' => '2.0',
                'id' => uniqid(),
                'method' => $method,
                'params' => $params
            ])->json() ?? [];
    }

    private function headers(string $url): array
    {
        $h = ['Content-Type'=>'application/json'];

        if (isset($this->sessions[$url])) {
            $h['Mcp-Session-Id'] = $this->sessions[$url];
        }

        return $h;
    }

    private function ollama(array $messages, array $tools): array
    {
        return Http::post($this->ollamaUrl, [
            'model' => $this->ollamaModel,
            'messages' => $messages,
            'tools' => $tools,
            'stream' => false
        ])->json() ?? [];
    }
}
