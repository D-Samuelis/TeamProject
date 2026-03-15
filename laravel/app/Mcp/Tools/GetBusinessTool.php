<?php

namespace App\Mcp\Tools;

use App\Models\Business\Business;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;


#[IsReadOnly(true)]
#[IsDestructive(false)]
#[IsOpenWorld(false)]
#[IsIdempotent(true)]
class GetBusinessTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        This tool allows you to search for businesses.

        This tool can retrieve information about a specific business based on its name, description, or ID,

        Use this tool when user asks for information about business.

        Use this tool when you want to find a business that belongs to a Service via ID.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'id' => 'nullable|string',
        ]);

        $query = Business::query();

        if (!empty($validated['id'])) {
            $query->where('id', $validated['id']);
            $result = $query->first();
            return Response::text($result);
        }

        if (!empty($validated['name'])) {
            $query->where('name', 'like', '%' . $validated['name'] . '%');
        }

        if (!empty($validated['description'])) {
            $query->where('description', 'like', '%' . $validated['description'] . '%');
        }

        $businesses = $query->limit(10)->get();

        if ($businesses->isEmpty()) {
            return Response::text('No businesses found matching your search.');
        }

        $result = $businesses->map(function ($business) {
            return 'Name: ' . $business->name . 'Description: ' . $business->description . "\n";
        });

        return Response::text($result);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('The name of the business.'),
            'description' => $schema->string()->description('A brief description of the business.'),
            'id' => $schema->string()->description('The unique identifier of the business.'),
        ];
    }
}
