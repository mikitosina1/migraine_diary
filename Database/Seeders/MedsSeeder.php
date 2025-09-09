<?php

namespace Modules\MigraineDiary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedsSeeder extends Seeder
{
	public function run(): void
	{
		$meds = [
			['code' => 'ibuprofen_600', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'aspirin', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'rizatriptan', 'created_at' => now(), 'updated_at' => now()],
			['code' => 'zomig', 'created_at' => now(), 'updated_at' => now()],
		];

		foreach ($meds as $med) {
			DB::table('migraine_meds')->updateOrInsert(['code' => $med['code']], $med);
		}

	}
}
