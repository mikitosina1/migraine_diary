<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class MigraineDiaryApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected const BASE_URL = '/api/v1/migraine-diary';

    protected const ADMIN_BASE_URL = '/api/v1/admin/migraine-diary';

    /**
     * acting test user as simple
     */
    protected function actingAsUser(): User
    {
        $user = $this->createUser();

        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * creates simple user
     */
    protected function createUser(): User
    {
        $role = Role::firstOrCreate(['title' => Role::USER]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    /**
     * acting test user as admin
     */
    protected function actingAsAdmin(): User
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        return $admin;
    }

    /**
     * creates admin user
     */
    protected function createAdmin(): User
    {
        $role = Role::firstOrCreate(['title' => Role::ADMIN]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}
