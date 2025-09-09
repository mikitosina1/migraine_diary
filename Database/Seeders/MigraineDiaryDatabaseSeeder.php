<?php

namespace Modules\MigraineDiary\Database\Seeders;

use Illuminate\Database\Seeder;

class MigraineDiaryDatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$this->call([
			SymptomsSeeder::class,
			TriggersSeeder::class,
			MedsSeeder::class,
			SymptomsTranslationsSeeder::class,
			TriggersTranslationsSeeder::class,
			MedsTranslationsSeeder::class,
		]);
	}
}
