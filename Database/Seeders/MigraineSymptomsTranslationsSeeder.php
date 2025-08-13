<?php

namespace Modules\MigraineDiary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigraineSymptomsTranslationsSeeder extends Seeder
{
	public function run(): void
	{
		$translations = [
			'nausea' => [
				['locale' => 'en', 'name' => 'Nausea', 'description' => 'Feeling of sickness with an inclination to vomit.'],
				['locale' => 'ru', 'name' => 'Тошнота', 'description' => 'Ощущение дурноты с позывами к рвоте.'],
				['locale' => 'de', 'name' => 'Übelkeit', 'description' => 'Gefühl von Krankheit mit Drang zum Erbrechen.'],
			],
			'photophobia' => [
				['locale' => 'en', 'name' => 'Photophobia', 'description' => 'Sensitivity to light.'],
				['locale' => 'ru', 'name' => 'Светобоязнь', 'description' => 'Повышенная чувствительность к свету.'],
				['locale' => 'de', 'name' => 'Photophobie', 'description' => 'Lichtempfindlichkeit.'],
			],
			'phonophobia' => [
				['locale' => 'en', 'name' => 'Phonophobia', 'description' => 'Sensitivity to sound.'],
				['locale' => 'ru', 'name' => 'Фонофобия', 'description' => 'Чувствительность к звукам.'],
				['locale' => 'de', 'name' => 'Phonophobie', 'description' => 'Lichtempfindlichkeit.'],
			],
			'aura' => [
				['locale' => 'en', 'name' => 'Aura', 'description' => 'Visual or sensory disturbances before migraine.'],
				['locale' => 'ru', 'name' => 'Аура', 'description' => 'Зрительные или сенсорные нарушения перед мигренью.'],
				['locale' => 'de', 'name' => 'Aura', 'description' => 'Visuelle oder sensorische Störungen vor Migräne.'],
			],
			'dizziness' => [
				['locale' => 'en', 'name' => 'Dizziness', 'description' => 'Feeling of unsteadiness or spinning.'],
				['locale' => 'ru', 'name' => 'Головокружение', 'description' => 'Ощущение нестабильности или вращения.'],
				['locale' => 'de', 'name' => 'Schwindel', 'description' => 'Gefühl von Unsicherheit oder Drehung.'],
			],
		];

		foreach ($translations as $code => $locales) {
			$symptom = DB::table('migraine_symptoms')->where('code', $code)->first();
			if (!$symptom) {
				continue;
			}

			foreach ($locales as $translation) {
				DB::table('migraine_symptom_translations')->updateOrInsert(
					['symptom_id' => $symptom->id, 'locale' => $translation['locale']],
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
