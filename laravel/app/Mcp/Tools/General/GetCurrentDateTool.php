<?php

namespace App\Mcp\Tools\General;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly(true)]
#[IsDestructive(false)]
#[IsOpenWorld(false)]
#[IsIdempotent(true)]
class GetCurrentDateTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        Returns the current date and time.

        ## When to use
        Use this tool everytime you deal with user prompts that require understanding of the current date and time context.
        This tool is especially useful when user prompt contains relative date references like
        "today", "tomorrow" or "next Friday"

        ## Required parameters
        None.

    MARKDOWN;

    public function handle(Request $request): Response
    {
        return Response::text(
            now()->toDateString()
        );
    }

    public function schema(JsonSchema $schema): array
    {
        return [

        ];
    }
}
