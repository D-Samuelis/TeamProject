<?php
 
namespace App\Mcp\Prompts;
 
use App\Models\Task;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;
 
class ProductivityReportPrompt extends Prompt
{
    protected string $description = 'Generates a productivity report analyzing task completion patterns over a specified time period.';
 
    public function handle(Request $request): array
    {
        $validated = $request->validate([
            'days' => ['sometimes', 'integer', 'min:1', 'max:90'],
            'tone' => ['sometimes', 'in:formal,casual,encouraging'],
        ]);
 
        $days = $validated['days'] ?? 7;
        $tone = $validated['tone'] ?? 'casual';
        $userId = $request->user()->id;
 
        // Look how expressive these queries are!
        $completedInPeriod = Task::forUser($userId)
            ->recentlyCompleted($days)
            ->count();
 
        $createdInPeriod = Task::forUser($userId)
            ->createdInPeriod($days)
            ->count();
 
        $stillIncomplete = Task::forUser($userId)
            ->incomplete()
            ->createdInPeriod($days)
            ->count();
 
        $highPriorityIncomplete = Task::forUser($userId)
            ->incomplete()
            ->highPriority()
            ->count();
 
        // Build context for the AI
        $context = "# Productivity Data ({$days} days)\n\n";
        $context .= "- Tasks completed: {$completedInPeriod}\n";
        $context .= "- Tasks created: {$createdInPeriod}\n";
        $context .= "- Still incomplete from this period: {$stillIncomplete}\n";
        $context .= "- High priority tasks pending: {$highPriorityIncomplete}\n";
 
        // Tone-specific instructions
        $toneInstructions = match($tone) {
            'formal' => 'Provide a professional, data-driven analysis suitable for a workplace report.',
            'encouraging' => 'Be motivating and positive, celebrating accomplishments and gently encouraging progress on pending tasks.',
            default => 'Be friendly and conversational, like a helpful colleague.',
        };
 
        return [
            Response::text(
                "You are a productivity analyst. Based on the following task data, " .
                "provide insights about the user's productivity. {$toneInstructions}\n\n" .
                "{$context}"
            )->asAssistant(),
 
            Response::text(
                "Please analyze my productivity over the last {$days} days and give me insights."
            ),
        ];
    }
 
    public function arguments(): array
    {
        return [
            new Argument(
                name: 'days',
                description: 'Number of days to analyze (1-90)',
                required: false
            ),
            new Argument(
                name: 'tone',
                description: 'Tone for the report: formal, casual, or encouraging',
                required: false
            ),
        ];
    }
}