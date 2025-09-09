<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Med Model
 *
 * Represents predefined migraine medications with multilingual support.
 * Provides standardized medication options that can be translated into different languages.
 *
 * @package Modules\MigraineDiary\App\Models
 *
 * @method static Builder|Med create(array $attributes = [])
 * @method static Builder|Med find($id, $columns = ['*'])
 * @method static Builder|Med findOrFail($id, $columns = ['*'])
 * @method static Builder|Med where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|Med whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder|Med orderBy($column, $direction = 'asc')
 * @method static Collection|Med[] get($columns = ['*'])
 * @method static Collection|Med[] all($columns = ['*'])
 */
class Med extends Model
{
	protected $table = 'migraine_meds';

	protected $fillable = ['code'];

	/**
	 * Get a list of medications with translations for the specified locale
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
	 * Get all translations for this medication
	 * @return HasMany
	 */
	public function translations(): HasMany
	{
		return $this->hasMany(MedTranslation::class, 'med_id');
	}

	/**
	 * Get the translated name for the current locale
	 * @return string
	 */
	public function getNameAttribute(): string
	{
		return $this->translations
			->where('locale', app()->getLocale())
			->first()?->name ?? $this->code;
	}
}
