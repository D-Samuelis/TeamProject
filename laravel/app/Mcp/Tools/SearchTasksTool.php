<?php
 
namespace App\Mcp\Tools;
 
use App\Models\Task;
//use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class SearchTasksTool extends Tool
{
    protected string $description = 'Searches for tasks by keyword, status, or priority. Returns matching tasks with their details.';
 
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:completed,incomplete,all'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);
 
        // Look at how clean this is with our custom builder!
        $query = Task::forUser($request->user()->id)
            ->search($validated['keyword'] ?? null)
            ->orderBy('created_at', 'desc');
 
        // Filter by status using our builder methods
        $status = $validated['status'] ?? 'all';
        if ($status === 'completed') {
            $query->completed();
        } elseif ($status === 'incomplete') {
            $query->incomplete();
        }
 
        // Filter by priority
        if ($priority = $validated['priority'] ?? null) {
            $query->priority($priority);
        }
 
        $limit = $validated['limit'] ?? 10;
        $tasks = $query->limit($limit)->get();
 
        if ($tasks->isEmpty()) {
            return Response::text("No tasks found matching your criteria.");
        }
 
        $output = "Found {$tasks->count()} task(s):\n\n";
 
        foreach ($tasks as $task) {
            $status = $task->completed ? '✅' : '⏳';
            $output .= "{$status} **[{$task->id}]** {$task->title}\n";
 
            if ($task->description) {
                $output .= "   {$task->description}\n";
            }
 
            $output .= "   Priority: {$task->priority}";
 
            if ($task->completed) {
                $output .= " | Completed: {$task->completed_at->format('M j, Y')}";
            }
 
            $output .= "\n\n";
        }
 
        return Response::text($output);
    }
 
    public function schema(JsonSchema $schema): array
    {
        return [
            'keyword' => $schema->string()
                ->description('Search for tasks containing this keyword in title or description'),
 
            'status' => $schema->string()
                ->enum(['completed', 'incomplete', 'all'])
                ->description('Filter by completion status')
                ->default('all'),
 
            'priority' => $schema->string()
                ->enum(['low', 'medium', 'high'])
                ->description('Filter by priority level'),
 
            'limit' => $schema->integer()
                ->description('Maximum number of tasks to return (1-50)')
                ->default(10),
        ];
    }
}