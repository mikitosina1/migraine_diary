<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class MigraineTrigger extends Model
{
	protected $table = 'migraine_triggers';

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

	public function translations(): HasMany
	{
		return $this->hasMany(MigraineTriggerTranslation::class, 'trigger_id');
	}

	public function getNameAttribute(): string
	{
		return $this->translations
			->where('locale', app()->getLocale())
			->first()?->name ?? $this->code;
	}
}
