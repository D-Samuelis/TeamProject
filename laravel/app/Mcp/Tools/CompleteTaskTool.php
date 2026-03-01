<?php
 
namespace App\Mcp\Tools;
 
use App\Models\Task;
//use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
 
class CompleteTaskTool extends Tool
{
    protected string $description = 'Marks a task as completed by its ID.';
 
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
        ], [
            'task_id.required' => 'You must specify which task to complete using its ID.',
            'task_id.exists' => 'No task found with that ID. Try searching for tasks first to find the correct ID.',
        ]);
 
        $task = Task::findOrFail($validated['task_id']);
 
        if ($task->completed) {
            return Response::text("ℹ️ This task was already completed on {$task->completed_at->format('M j, Y')}.");
        }
 
        $task->markComplete();
 
        return Response::text(
            "✅ Task completed!\n\n" .
            "**{$task->title}**\n" .
            "Completed: {$task->completed_at->format('M j, Y \a\t g:i A')}"
        );
    }
 
    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()
                ->description('The ID of the task to mark as complete')
                ->required(),
        ];
    }
}