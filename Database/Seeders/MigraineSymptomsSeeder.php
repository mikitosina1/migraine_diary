<?php

namespace Modules\MigraineDiary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigraineSymptomsSeeder extends Seeder
{
	public function run(): void
	{
		$symptoms = [
			['code' => 'nausea', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'photophobia', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'phonophobia', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'aura', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'dizziness', 'created_at' => now(), 'updated_at' => now()],
		];

		foreach ($symptoms as $symptom) {
			DB::table('migraine_symptoms')->updateOrInsert(['code' => $symptom['code']], $symptom);
		}
	}
}
