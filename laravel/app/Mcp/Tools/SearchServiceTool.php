<?php

namespace App\Mcp\Tools;

use App\Models\Business\Service;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class SearchServiceTool extends Tool
{

    protected string $description = <<<'MARKDOWN'
        This tool allows users to search for available services.

        Users can specify the type of service they are looking for, such as "haircut", "massage", or "dental cleaning".
        Users can also provide additional details to narrow down their search, such as location, price range, or specific service providers.

        The tool will return a list of matching services.
    MARKDOWN;


    public function handle(Request $request): Response
    {

        $validated = $request->validate([
            'keyword' => 'required|string',
            'location' => 'nullable|string',
            'price_low_range' => 'nullable|string',
            'price_high_range' => 'nullable|string',
            'provider' => 'nullable|string',
        ]);

        if (empty($validated['keyword'])) {
            return Response::text('Please provide a keyword to search for services.');
        }

        $query = Service::query()
            ->where('is_active', true)
            ->where(function ($q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['keyword'] . '%')
                    ->orWhere('description', 'like', '%' . $validated['keyword'] . '%');
            });

        if (!empty($validated['location'])) {
            $query->where('location_type', 'like', '%' . $validated['location'] . '%');
        }

        if (!empty($validated['provider'])) {
            $query->whereHas('business', function ($q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['provider'] . '%');
            });
        }

        if(!empty($validated['price_low_range']) || !empty($validated['price_high_range'])) {

            $low = !empty($validated['price_low_range']) ? (float)$validated['price_low_range'] : 0;
            $high = !empty($validated['price_high_range']) ? (float)$validated['price_high_range'] : PHP_INT_MAX;

            $query->whereBetween('price', [$low, $high]);
        }

        $services = $query->limit(10)->get();

        if ($services->isEmpty()) {
            return Response::text('No services found matching your search.');
        }

        $result = $services->map(function ($service) {
            return "{$service->name} - {$service->price}$ ({$service->duration_minutes} {$service->business->name})";
        })->implode("\n");

        return Response::text($result);
    }


    public function schema(JsonSchema $schema): array
    {
        return [
            'keyword' => $schema->string()
                ->required()
                ->description('The type of service the user is looking for.'),
            'location' => $schema->string()->description('The location where the user wants to find the service.'),
            'price_low_range' => $schema->string()->description('The minimum price for the service.'),
            'price_high_range' => $schema->string()->description('The maximum price for the service.'),
        ];
    }
}
