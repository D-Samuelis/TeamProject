<?php
 
namespace App\Mcp\Resources;
 
use App\Models\Task;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;
 
class RecentCompletedTasksResource extends Resource
{
    protected string $description = 'Shows the 20 most recently completed tasks with completion dates.';
 
    protected string $uri = 'tasks://completed/recent';
 
    public function handle(Request $request): Response
    {
        // One line, crystal clear intent
        $completedTasks = Task::forUser($request->user()->id)
            ->completed()
            ->orderBy('completed_at', 'desc')
            ->limit(20)
            ->get();
 
        if ($completedTasks->isEmpty()) {
            return Response::text("No completed tasks yet.");
        }
 
        $output = "# Recently Completed Tasks\n\n";
 
        foreach ($completedTasks as $task) {
            $output .= "✅ **{$task->title}**\n";
            $output .= "   Completed: {$task->completed_at->diffForHumans()}\n";
            $output .= "   Priority was: {$task->priority}\n\n";
        }
 
        return Response::text($output);
    }
}