<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class MigraineSymptom extends Model
{
	protected $table = 'migraine_symptoms';

	protected $fillable = ['code'];

	/**
	 * get a list of migraine symptoms with translations
	 * @param string $locale
	 * @return Collection
	 */
	public static function getListWithTranslations(string $locale = 'en'): Collection
	{
		$locale = session('locale') ?? app()->getLocale() ?? $locale;

		return self::with(['translations' => function($query) use ($locale) {
			$query->where('locale', $locale);
		}])->get()->map(function($item) {
			return [
				'id' => $item->id,
				'code' => $item->code,
				'name' => $item->translations->first()->name ?? $item->code
			];
		});
	}

	/**
	 * return translations (one-to-many)
	 * @return HasMany
	 */
	public function translations(): HasMany
	{
		return $this->hasMany(MigraineSymptomTranslation::class, 'symptom_id');
	}

	/**
	 * dynamic property for name on current language
	 * @return string
	 */
	public function getNameAttribute(): string
	{
		return $this->translations
			->where('locale', app()->getLocale())
			->first()?->name ?? $this->code;
	}
}
