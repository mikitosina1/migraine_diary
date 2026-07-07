<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1\Admin;

use Modules\MigraineDiary\Tests\Feature\Api\V1\MigraineDiaryApiTestCase;

class DictionaryApiTest extends MigraineDiaryApiTestCase
{
    public function test_admin_can_list_all_dictionaries(): void
    {
        $this->actingAsAdmin();
        $this->createSymptom();
        $this->createTrigger();
        $this->createMed();

        $response = $this->get(self::ADMIN_BASE_URL.'/dictionaries');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'symptoms',
                    'triggers',
                    'meds',
                ],
            ]);
    }

    public function test_admin_can_list_dictionary_by_type(): void
    {
        $this->actingAsAdmin();
        $symptom = $this->createSymptom();
        $trigger = $this->createTrigger();
        $med = $this->createMed();

        $this->get(self::ADMIN_BASE_URL.'/dictionaries/symptoms')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $symptom->id)
            ->assertJsonPath('data.0.code', $symptom->code);

        $this->get(self::ADMIN_BASE_URL.'/dictionaries/triggers')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $trigger->id)
            ->assertJsonPath('data.0.code', $trigger->code);

        $this->get(self::ADMIN_BASE_URL.'/dictionaries/meds')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $med->id)
            ->assertJsonPath('data.0.code', $med->code);
    }

    public function test_admin_dictionary_errors_are_json_without_accept_header(): void
    {
        $response = $this->get(self::ADMIN_BASE_URL.'/dictionaries');

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);

        $this->assertStringStartsWith(
            'application/json',
            $response->headers->get('content-type')
        );

    }
}
