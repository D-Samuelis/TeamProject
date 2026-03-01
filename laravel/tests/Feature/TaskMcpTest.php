<?php

namespace Tests\Feature;

use App\Mcp\Servers\TaskServer;
use App\Mcp\Tools\CreateTaskTool;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskMcpTest extends TestCase
{
    use RefreshDatabase;
    public function test_can_create_a_task_with_all_fields(): void
    {
        $response = TaskServer::tool(CreateTaskTool::class, [
            'title' => 'Write tutorial',
            'description' => 'Complete the Laravel MCP tutorial',
            'priority' => 'high',
        ]);

        $response->assertOk();
        $response->assertSee('Write tutorial');
        $response->assertSee('high');

        $task = Task::first();
        $this->assertEquals('Write tutorial', $task->title);
        $this->assertEquals('high', $task->priority);
        $this->assertFalse($task->completed);
    }

    public function test_task_title_is_required(): void
    {
        $response = TaskServer::tool(CreateTaskTool::class, [
            'description' => 'A task without a title',
        ]);

        $response->assertHasErrors();
    }

    public function test_invalid_priority_is_rejected(): void
    {
        $response = TaskServer::tool(CreateTaskTool::class, [
            'title' => 'Test task',
            'priority' => 'super-urgent',
        ]);

        $response->assertHasErrors();
    }

    public function test_creates_task_with_default_priority_when_not_specified(): void
    {
        $response = TaskServer::tool(CreateTaskTool::class, [
            'title' => 'Simple task',
        ]);

        $response->assertOk();

        $task = Task::first();
        $this->assertEquals('medium', $task->priority);
    }
}