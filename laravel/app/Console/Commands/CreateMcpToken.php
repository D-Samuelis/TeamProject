<?php
 
namespace App\Console\Commands;
 
use App\Models\Auth\User;
use Illuminate\Console\Command;
 
class CreateMcpToken extends Command
{
    protected $signature = 'mcp:token {email}';
    protected $description = 'Create an MCP API token for a user';
 
    public function handle(): void
    {
        $user = User::where('email', $this->argument('email'))->first();
 
        if (!$user) {
            $this->error('User not found');
            return;
        }
 
        $token = $user->createToken('mcp-access')->plainTextToken;
 
        $this->info('Token created successfully:');
        $this->line($token);
        $this->newLine();
        $this->info('Use this in your Authorization header:');
        $this->line("Bearer {$token}");
    }
}