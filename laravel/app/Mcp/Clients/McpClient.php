<?php

namespace App\Mcp\Clients;

use Illuminate\Support\Facades\Http;

class McpClient
{
    private string $serverUrl;
    private string $ollamaUrl;
    private string $model;

    private ?string $sessionId = null;
    private array $tools;

    public function __construct()
    {
        $this->serverUrl = config('mcp.url');
        $this->ollamaUrl = config('mcp.ollama_url');
        $this->model     = config('mcp.ollama_model');
        $this->tools = $this->initializeServer();
    }

    public function chat(string $message): string
    {
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant. Today is '.now()],
            ['role' => 'user', 'content' => $message],
        ];

        $reply = $this->ollama($messages);

        while (!empty($reply['tool_calls'])) {

            foreach ($reply['tool_calls'] as $index => $call) {

                $name = $call['function']['name'];
                $args = $call['function']['arguments'] ?? [];

                if (is_string($args)) {
                    $args = json_decode($args, true) ?? [];
                }

                echo "  [" . ($index + 1) . "] Tool: {$name}\n";
                echo "      Args: " . json_encode($args, JSON_PRETTY_PRINT) . "\n";

                $result = $this->rpc('tools/call', [
                    'name' => $name,
                    'arguments' => $args
                ]);

                $messages[] = [
                    'role' => 'assistant',
                    'tool_calls' => [$call]
                ];

                $messages[] = [
                    'role' => 'tool',
                    'content' => collect($result['result']['content'] ?? [])
                        ->pluck('text')
                        ->implode('')
                ];
            }

            $reply = $this->ollama($messages);
        }

        return $reply['content'] ?? 'No content';
    }

    private function initializeServer(): array
    {
        $init = $this->rpc('initialize', [
            'protocolVersion' => '2024-11-05',
            'capabilities' => [],
            'clientInfo' => [
                'name' => 'LaravelMCP',
                'version' => '1.0'
            ],
        ]);

        $this->sessionId = $init['result']['_sessionId'] ?? null;

        $this->rpc('notifications/initialized', [], true);

        $tools = $this->rpc('tools/list')['result']['tools'] ?? [];

        return collect($tools)->map(fn ($tool) => [
            'type' => 'function',
            'function' => [
                'name' => $tool['name'],
                'description' => $tool['description'] ?? '',
                'parameters' => $tool['inputSchema'] ?? [
                        'type' => 'object',
                        'properties' => []
                    ],
            ]
        ])->values()->all();
    }

    private function rpc(string $method, array $params = [], bool $notification = false): array
    {
        $response = Http::withHeaders($this->headers())->post($this->serverUrl, [
            'jsonrpc' => '2.0',
            'id' => uniqid(),
            'method' => $method,
            'params' => $params
        ]);

        return $notification ? [] : ($response->json() ?? []);
    }

    private function headers(): array
    {
        return array_filter([
            'Content-Type' => 'application/json',
            'Mcp-Session-Id' => $this->sessionId
        ]);
    }

    private function ollama(array $messages): array
    {
        $response = Http::post($this->ollamaUrl, [
            'model' => $this->model,
            'messages' => $messages,
            'tools' => $this->tools,
            'stream' => false
        ]);

         return $response->json()['message'] ?? [];
    }
}
