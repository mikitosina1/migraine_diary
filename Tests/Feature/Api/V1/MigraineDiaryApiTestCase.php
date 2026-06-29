<?php

namespace Modules\MigraineDiary\Tests\Feature\Api\V1;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Models\Med;
use Modules\MigraineDiary\App\Models\Symptom;
use Modules\MigraineDiary\App\Models\Trigger;
use Modules\MigraineDiary\App\Models\UserMed;
use Modules\MigraineDiary\App\Models\UserSymptom;
use Modules\MigraineDiary\App\Models\UserTrigger;
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

    protected function createSymptom(): Symptom
    {
        $symptom = Symptom::create([
            'code' => fake()->unique()->word(),
        ]);

        $symptom->translations()->create([
            'locale' => 'en',
            'name' => 'Nausea',
            'description' => 'Feeling sick.',
        ]);

        return $symptom;
    }

    protected function createUserSymptom(int $uid, array $attributes = []): UserSymptom
    {
        return UserSymptom::create(array_merge([
            'user_id' => $uid,
            'name' => 'Nausea user testing',
            'description' => 'Created by User Symptom Nausea',
        ], $attributes));
    }

    protected function createTrigger(): Trigger
    {
        $trigger = Trigger::create([
            'code' => fake()->unique()->word(),
        ]);

        $trigger->translations()->create([
            'locale' => 'en',
            'name' => 'Stress for testing',
            'description' => 'Feeling tested with stress.',
        ]);

        return $trigger;
    }

    protected function createUserTrigger(int $uid, array $attributes = []): UserTrigger
    {
        return UserTrigger::create(array_merge([
            'user_id' => $uid,
            'name' => 'Light user testing',
            'description' => 'Created by User Trigger Light',
        ], $attributes));
    }

    protected function createMed(): Med
    {
        $med = Med::create([
            'code' => fake()->unique()->word(),
        ]);

        $med->translations()->create([
            'locale' => 'en',
            'name' => 'Ibuprofen 400 mg.',
            'description' => 'Pain reliever and anti-inflammatory.',
        ]);

        return $med;
    }

    protected function createUserMed(int $uid, array $attributes = []): UserMed
    {
        return UserMed::create(array_merge([
            'user_id' => $uid,
            'name' => 'IBU 200 user testing',
            'description' => 'Created by User med IBU 200',
        ], $attributes));
    }

    protected function createAttack(int $uid, array $attributes = []): Attack
    {
        return Attack::create(array_merge([
            'user_id' => $uid,
            'start_time' => now(),
            'pain_level' => rand(1, 10),
            'notes' => 'Test attack'.rand(1, 99),
        ], $attributes));
    }

    protected function createAttacks(int $uid, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            Attack::create([
                'user_id' => $uid,
                'start_time' => now(),
                'pain_level' => rand(1, 10),
                'notes' => 'Test attack'.$i,
            ]);
        }
    }
}
