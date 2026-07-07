<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1\User;

use Modules\MigraineDiary\Tests\Feature\Api\V1\MigraineDiaryApiTestCase;

class ReportApiTest extends MigraineDiaryApiTestCase
{
    public function test_user_can_get_excel(): void
    {
        $this->actingAsUser();
        $response = $this->postJson(self::BASE_URL.'/reports/excel', [
            'period' => 'year',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_get_pdf(): void
    {
        $this->actingAsUser();
        $response = $this->postJson(self::BASE_URL.'/reports/pdf', [
            'period' => 'year',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_send_email(): void
    {
        $this->actingAsUser();
        $response = $this->postJson(self::BASE_URL.'/reports/email', [
            'recipient_type' => 'self',
            'period' => 'year',
            'doctor_email' => 'doctor@example.com',
            'user_name' => 'Austin',
            'user_lastname' => 'Prostin',
            'formats' => ['pdf', 'excel'],
        ]);

        $response->assertStatus(200);
        $response->json('data.message');
    }
}
