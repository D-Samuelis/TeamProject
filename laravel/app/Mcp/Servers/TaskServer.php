<?php
 
namespace App\Mcp\Servers;
 
use App\Mcp\Prompts\ProductivityReportPrompt;
use App\Mcp\Resources\RecentCompletedTasksResource;
use App\Mcp\Resources\TaskStatsResource;
use App\Mcp\Tools\CompleteTaskTool;
use App\Mcp\Tools\CreateTaskTool;
use App\Mcp\Tools\SearchTasksTool;
use Laravel\Mcp\Server;
 
class TaskServer extends Server
{
    protected array $tools = [
        CreateTaskTool::class,
        CompleteTaskTool::class,
        SearchTasksTool::class,
    ];
 
    protected array $resources = [
        TaskStatsResource::class,
        RecentCompletedTasksResource::class,
    ];
 
    protected array $prompts = [
        ProductivityReportPrompt::class,
    ];
}