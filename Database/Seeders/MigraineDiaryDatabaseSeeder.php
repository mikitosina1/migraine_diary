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
			MigraineSymptomsSeeder::class,
			MigraineTriggersSeeder::class,
			MigraineMedsSeeder::class,
			MigraineSymptomsTranslationsSeeder::class,
			MigraineTriggersTranslationsSeeder::class,
			MigraineMedTranslationsSeeder::class,
		]);
	}
}
