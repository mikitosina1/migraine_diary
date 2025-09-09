<?php

namespace Modules\MigraineDiary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TriggersTranslationsSeeder extends Seeder
{
	public function run(): void
	{
		$translations = [
			'stress' => [
				['locale' => 'en', 'name' => 'Stress', 'description' => 'Emotional or physical stress.'],
				['locale' => 'ru', 'name' => 'Стресс', 'description' => 'Эмоциональное или физическое напряжение.'],
				['locale' => 'de', 'name' => 'Stress', 'description' => 'Emotionaler oder körperlicher Stress.'],
			],
			'lack_of_sleep' => [
				['locale' => 'en', 'name' => 'Lack of Sleep', 'description' => 'Insufficient sleep duration or quality.'],
				['locale' => 'ru', 'name' => 'Недостаток сна', 'description' => 'Недостаточная продолжительность или качество сна.'],
				['locale' => 'de', 'name' => 'Schlafmangel', 'description' => 'Unzureichende Schlafdauer oder -qualität.'],
			],
			'certain_foods' => [
				['locale' => 'en', 'name' => 'Certain Foods', 'description' => 'Specific foods that may trigger migraines.'],
				['locale' => 'ru', 'name' => 'Определённые продукты', 'description' => 'Некоторые продукты, которые могут вызывать мигрень.'],
				['locale' => 'de', 'name' => 'Bestimmte Lebensmittel', 'description' => 'Bestimmte Lebensmittel, die Migräne auslösen können.'],
			],
			'hormonal_changes' => [
				['locale' => 'en', 'name' => 'Hormonal Changes', 'description' => 'Changes in hormone levels that may trigger migraines.'],
				['locale' => 'ru', 'name' => 'Гормональные изменения', 'description' => 'Изменения уровня гормонов, которые могут вызывать мигрень.'],
				['locale' => 'de', 'name' => 'Hormonelle Veränderungen', 'description' => 'Veränderungen des Hormonspiegels, die Migräne auslösen können.'],
			],
			'weather_changes' => [
				['locale' => 'en', 'name' => 'Weather Changes', 'description' => 'Changes in weather conditions that may trigger migraines.'],
				['locale' => 'ru', 'name' => 'Изменения погоды', 'description' => 'Изменения погодных условий, которые могут вызывать мигрень.'],
				['locale' => 'de', 'name' => 'Wetteränderungen', 'description' => 'Änderungen der Wetterbedingungen, die Migräne auslösen können.'],
			],
		];

		foreach ($translations as $code => $locales) {
			$trigger = DB::table('migraine_triggers')->where('code', $code)->first();
			if (!$trigger) {
				continue;
			}

			foreach ($locales as $translation) {
				DB::table('migraine_trigger_translations')->updateOrInsert(
					['trigger_id' => $trigger->id, 'locale' => $translation['locale']],
					[
						'name' => $translation['name'],
						'description' => $translation['description'],
						'updated_at' => now(),
						'created_at' => now(),
					]
				);
			}
		}
	}
}
