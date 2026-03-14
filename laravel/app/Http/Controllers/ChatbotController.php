<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    private string $ollamaUrl;
    private string $model;
    private string $mcpServiceUrl;

    public function __construct()
    {
        $this->ollamaUrl = config('mcp.ollama_url');
        $this->model = config('mcp.ollama_model');
        $this->mcpServiceUrl = config('mcp.mcp_service_url');
    }

    public function index()
    {
        return view('chatbot');
    }

    public function message(Request $request)
    {
        $messages = $request->input('messages', []);

        $response = $this->callOllama($messages);

        $reply = $response['message']['content'] ?? '(no response)';

        return response()->json(['reply' => $reply]);
    }


    private function callOllama(array $messages): array
    {
        $response = Http::timeout(120)->post($this->ollamaUrl, [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => false
        ]);

        if ($response->failed()) {
            throw new \RuntimeException($response->body());
        }

        return $response->json();
    }

    private function callMcpService(array $messages): array
    {
        $response = Http::timeout(120)->post("{$this->mcpServiceUrl}/chat", [
            'messages' => $messages,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('MCP service error: ' . $response->body());
        }

        return $response->json();
    }

}






