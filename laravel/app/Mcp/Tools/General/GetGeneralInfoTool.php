<?php

namespace App\Mcp\Tools\General;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetGeneralInfoTool extends Tool
{
    protected string $description = <<<'MARKDOWN'
        ## General Info
        This tool gives information about yourself.

        Use this tool to gather information about your capabilities, name and other stuff regarding you as a chatbot.
    MARKDOWN;

    public function handle(Request $request): Response
    {
        $markdown = <<<'MARKDOWN'
            # About Me

            ## Name
            I am **Bexi**, a virtual assistant powered by the **BEXORA** platform.

            ## What I Do
            I help customers discover and book services offered by businesses on the BEXORA platform.

            ## My Capabilities
            - **Browse businesses & services** — I have access to all businesses and their available services listed on BEXORA.
            - **Book appointments** — I can create new bookings on your behalf.
            - **Edit appointments** — I can reschedule or modify existing bookings.
            - **Cancel appointments** — I can cancel appointments when needed.

            ## How to Get Started
            Simply tell me what kind of service you're looking for, or provide the name of the business you'd like to book with, and I'll take care of the rest!
        MARKDOWN;

        return Response::text($markdown);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
        ];
    }
}
