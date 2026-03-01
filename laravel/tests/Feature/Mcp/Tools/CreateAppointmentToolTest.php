<?php

namespace Feature\Mcp\Tools;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Mcp\Servers\AppointmentServer;
use Tests\TestCase;
use App\Mcp\Tools\CreateAppointmentTool;

class CreateAppointmentToolTest extends TestCase
{
    public function test_can_create_an_appointment_with_all_fields(): void
    {
        $response = AppointmentServer::tool(CreateAppointmentTool::class, [
            'description' => 'Dental check-up appointment',
            'time' => '2026-03-05 10:00',
        ]);

        $response->assertOk();
        $response->assertSee('Dental check-up appointment');
        $response->assertSee('2026-03-05 10:00');
    }

    public function test_description_is_required(): void
    {
        $response = AppointmentServer::tool(CreateAppointmentTool::class, [
            'time' => '2026-03-05 10:00',
        ]);

        $response->assertHasErrors();
    }

    public function test_time_is_required(): void
    {
        $response = AppointmentServer::tool(CreateAppointmentTool::class, [
            'description' => 'Dental check-up appointment',
        ]);

        $response->assertHasErrors();
    }

    public function test_description_must_be_at_least_10_characters(): void
    {
        $response = AppointmentServer::tool(CreateAppointmentTool::class, [
            'description' => 'Haircut',
            'time' => '2026-03-05 10:00',
        ]);

        $response->assertHasErrors();
    }

    public function test_description_cannot_exceed_50_characters(): void
    {
        $response = AppointmentServer::tool(CreateAppointmentTool::class, [
            'description' => str_repeat('a', 51),
            'time' => '2026-03-05 10:00',
        ]);

        $response->assertHasErrors();
    }

    public function test_accepts_vague_time_description(): void
    {
        $response = AppointmentServer::tool(CreateAppointmentTool::class, [
            'description' => 'Consultation with Dr. Smith',
            'time' => 'immediately',
        ]);

        $response->assertOk();
    }
}
