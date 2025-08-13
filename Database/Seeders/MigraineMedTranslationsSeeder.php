<?php

namespace Modules\MigraineDiary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigraineMedTranslationsSeeder extends Seeder
{
	public function run(): void
	{
		$translations = [
			'ibuprofen_600' => [
				['locale' => 'en', 'name' => 'Ibuprofen 600mg', 'description' => 'Pain reliever and anti-inflammatory.'],
				['locale' => 'ru', 'name' => 'Ибупрофен 600мг', 'description' => 'Обезболивающее и противовоспалительное средство.'],
				['locale' => 'de', 'name' => 'Ibuprofen 600mg', 'description' => 'Schmerzmittel und Entzündungshemmer.'],
			],
			'aspirin' => [
				['locale' => 'en', 'name' => 'Aspirin', 'description' => 'Pain reliever and blood thinner.'],
				['locale' => 'ru', 'name' => 'Аспирин', 'description' => 'Обезболивающее и разжижитель крови.'],
				['locale' => 'de', 'name' => 'Aspirin', 'description' => 'Schmerzmittel und Blutverdünner.'],
			],
			'rizatriptan' => [
				['locale' => 'en', 'name' => 'Rizatriptan', 'description' => 'Migraine-specific medication.'],
				['locale' => 'ru', 'name' => 'Ризатриптан', 'description' => 'Медикамент для лечения мигрени.'],
				['locale' => 'de', 'name' => 'Rizatriptan', 'description' => 'Medikament gegen Migräne.'],
			],
			'zomig' => [
				['locale' => 'en', 'name' => 'Zomig', 'description' => 'Brand name for zolmitriptan.'],
				['locale' => 'ru', 'name' => 'Зомиг', 'description' => 'Торговое название золмитриптана.'],
				['locale' => 'de', 'name' => 'Zomig', 'description' => 'Markenname für Zolmitriptan.'],
			],
		];

		foreach ($translations as $code => $locales) {
			$med = DB::table('migraine_meds')->where('code', $code)->first();
			if (!$med) {
				continue;
			}

			foreach ($locales as $translation) {
				DB::table('migraine_med_translations')->updateOrInsert(
					['med_id' => $med->id, 'locale' => $translation['locale']],
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
