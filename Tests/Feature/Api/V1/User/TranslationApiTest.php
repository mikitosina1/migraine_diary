<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1\User;

use Modules\MigraineDiary\Tests\Feature\Api\V1\MigraineDiaryApiTestCase;

class TranslationApiTest extends MigraineDiaryApiTestCase
{
    public function test_user_can_get_translations(): void
    {
        $this->actingAsUser();
        $response = $this->get(self::BASE_URL.'/translations');
        $response->assertStatus(200)
            ->json('meta.locale');
    }
}
