<?php
 
namespace App\Mcp\Resources;
 
use App\Models\Task;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;
 
class TaskStatsResource extends Resource
{
    protected string $description = 'Provides statistical overview of all tasks including completion rates and priority breakdown.';
 
    protected string $uri = 'tasks://stats';
 
    public function handle(Request $request): Response
    {
        $userId = $request->user()->id;
 
        // Beautiful, readable queries thanks to our builder
        $totalTasks = Task::forUser($userId)->count();
        $completedTasks = Task::forUser($userId)->completed()->count();
        $incompleteTasks = Task::forUser($userId)->incomplete()->count();
 
        $completionRate = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100, 1)
            : 0;
 
        $priorityBreakdown = Task::forUser($userId)
            ->incomplete()
            ->selectRaw('priority, count(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
 
        $stats = "# Task Statistics\n\n";
        $stats .= "**Total Tasks:** {$totalTasks}\n";
        $stats .= "**Completed:** {$completedTasks}\n";
        $stats .= "**Incomplete:** {$incompleteTasks}\n";
        $stats .= "**Completion Rate:** {$completionRate}%\n\n";
 
        if (!empty($priorityBreakdown)) {
            $stats .= "## Incomplete Tasks by Priority\n";
            foreach (['high', 'medium', 'low'] as $priority) {
                $count = $priorityBreakdown[$priority] ?? 0;
                $stats .= "- **" . ucfirst($priority) . ":** {$count}\n";
            }
        }
 
        return Response::text($stats);
    }
}