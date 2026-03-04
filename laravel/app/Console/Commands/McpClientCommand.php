<?php

namespace App\Console\Commands;

use App\Mcp\Clients\McpClient;
use Illuminate\Console\Command;

class McpClientCommand extends Command
{
    protected $signature   = 'mcp:client {message? : The message to send}';
    protected $description = 'Send a message through Ollama with MCP tools';

    public function handle(McpClient $service)
    {
        $userMessage = $this->argument('message')
            ?? $this->ask('What is your message?');

        $this->info('Thinking...');

        try {
            $response = $service->chat($userMessage);
            $this->newLine();
            $this->line('<fg=cyan>Ollama:</> ' . $response);
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
