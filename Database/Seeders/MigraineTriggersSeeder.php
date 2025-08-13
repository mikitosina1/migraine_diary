<?php

namespace Modules\MigraineDiary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigraineTriggersSeeder extends Seeder
{
	public function run(): void
	{
		$triggers = [
			['code' => 'stress', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'lack_of_sleep', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'certain_foods', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'hormonal_changes', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'weather_changes', 'created_at' => now(), 'updated_at' => now()],
		];

		foreach ($triggers as $trigger) {
			DB::table('migraine_triggers')->updateOrInsert(['code' => $trigger['code']], $trigger);
		}
	}
}
