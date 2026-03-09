<?php

namespace App\Console\Commands;

use App\Mcp\Clients\McpClient;
use Illuminate\Console\Command;

class McpClientCommand extends Command
{
    protected $signature   = 'mcp:client';
    protected $description = 'Chat with Ollama using MCP tools';

    public function handle(McpClient $service)
    {
        $this->info('MCP Chat started. Type "exit" or "quit" to stop.');
        $this->newLine();

        while (true) {
            $userMessage = $this->ask('<fg=green>You</>');

            if (in_array(strtolower(trim($userMessage)), ['exit', 'quit', 'q'])) {
                $this->info('Goodbye!');
                break;
            }

            if (blank($userMessage)) {
                continue;
            }

            $this->info('Thinking...');

            try {
                $response = $service->chat($userMessage);
                $this->newLine();
                $this->line('<fg=cyan>Ollama:</> ' . $response);
                $this->newLine();
            } catch (\Throwable $e) {
                $this->error('Error: ' . $e->getMessage());
            }
        }
    }
}
