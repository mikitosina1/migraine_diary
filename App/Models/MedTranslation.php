<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * MedTranslation Model
 *
 * Stores translated names and descriptions for predefined medications.
 * Supports multiple languages for internationalization.
 *
 * @package Modules\MigraineDiary\App\Models
 *
 * @method static Builder|MedTranslation create(array $attributes = [])
 * @method static Builder|MedTranslation find($id, $columns = ['*'])
 * @method static Builder|MedTranslation findOrFail($id, $columns = ['*'])
 * @method static Builder|MedTranslation where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|MedTranslation whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder|MedTranslation orderBy($column, $direction = 'asc')
 * @method static Collection|MedTranslation[] get($columns = ['*'])
 * @method static Collection|MedTranslation[] all($columns = ['*'])
 */
class MedTranslation extends Model
{
	public $timestamps = false;
	protected $table = 'migraine_med_translations';
	protected $fillable = [
		'med_id',
		'locale',
		'name',
		'description'
	];
}
