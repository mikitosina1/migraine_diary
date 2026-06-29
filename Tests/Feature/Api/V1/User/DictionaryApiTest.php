<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1\User;

use Modules\MigraineDiary\Tests\Feature\Api\V1\MigraineDiaryApiTestCase;

class DictionaryApiTest extends MigraineDiaryApiTestCase
{
    public function test_user_can_get_dictionaries(): void
    {
        $this->actingAsUser();

        $response = $this->json('GET', self::BASE_URL.'/dictionaries');
        $response->assertStatus(200);
    }

    public function test_dictionaries_include_user_custom_entities(): void
    {
        $user = $this->actingAsUser();
        $userMed = $this->createUserMed($user->id, [
            'name' => 'IBU 200 user testing as well',
        ]);
        $userSymptom = $this->createUserSymptom($user->id, [
            'name' => 'Nausea eaeao',
        ]);
        $userTrigger = $this->createUserTrigger($user->id, [
            'name' => 'Light user testing, hard developer sleeping',
        ]);

        $response = $this->json('GET', self::BASE_URL.'/dictionaries');
        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $userSymptom->id,
                'name' => $userSymptom->name,
            ])
            ->assertJsonFragment([
                'id' => $userTrigger->id,
                'name' => $userTrigger->name,
            ])
            ->assertJsonFragment([
                'id' => $userMed->id,
                'name' => $userMed->name,
            ]);
    }

    public function test_dictionaries_do_not_include_foreign_custom_entities(): void
    {
        $this->actingAsUser();
        $foreignUser = $this->createUser();
        $foreignUserMed = $this->createUserMed($foreignUser->id, [
            'name' => 'IBU 200 user testing as well',
        ]);
        $foreignUserSymptom = $this->createUserSymptom($foreignUser->id, [
            'name' => 'Nausea eaeao',
        ]);
        $foreignUserTrigger = $this->createUserTrigger($foreignUser->id, [
            'name' => 'Light user testing, hard developer sleeping',
        ]);

        $response = $this->json('GET', self::BASE_URL.'/dictionaries');
        $response
            ->assertOk()
            ->assertJsonMissing([
                'id' => $foreignUserSymptom->id,
                'name' => $foreignUserSymptom->name,
            ])
            ->assertJsonMissing([
                'id' => $foreignUserTrigger->id,
                'name' => $foreignUserTrigger->name,
            ])
            ->assertJsonMissing([
                'id' => $foreignUserMed->id,
                'name' => $foreignUserMed->name,
            ]);
    }
}
