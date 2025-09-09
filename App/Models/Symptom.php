<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Symptom Model
 *
 * Represents predefined migraine symptoms with multilingual support.
 * Provides standardized symptom options that can be translated into different languages.
 *
 * @package Modules\MigraineDiary\App\Models
 *
 * @method static Builder|Symptom create(array $attributes = [])
 * @method static Builder|Symptom find($id, $columns = ['*'])
 * @method static Builder|Symptom findOrFail($id, $columns = ['*'])
 * @method static Builder|Symptom where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|Symptom whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder|Symptom orderBy($column, $direction = 'asc')
 * @method static Collection|Symptom[] get($columns = ['*'])
 * @method static Collection|Symptom[] all($columns = ['*'])
 */
class Symptom extends Model
{
	protected $table = 'migraine_symptoms';

	protected $fillable = ['code',
		'locale',
		'name'
	];

	/**
	 * Get a list of symptoms with translations for the specified locale
	 *
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
	 * Get all translations for this symptom
	 *
	 * @return HasMany
	 */
	public function translations(): HasMany
	{
		return $this->hasMany(SymptomTranslation::class, 'symptom_id');
	}

	/**
	 * Get the translated name for the current locale
	 *
	 * @return string
	 */
	public function getNameAttribute(): string
	{
		return $this->translations
			->where('locale', app()->getLocale())
			->first()?->name ?? $this->code;
	}
}
