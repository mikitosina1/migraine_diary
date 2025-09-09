<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * SymptomTranslation Model
 *
 * Stores translated names and descriptions for predefined symptoms.
 * Supports multiple languages for internationalization.
 *
 * @package Modules\MigraineDiary\App\Models
 *
 * @method static Builder|SymptomTranslation create(array $attributes = [])
 * @method static Builder|SymptomTranslation find($id, $columns = ['*'])
 * @method static Builder|SymptomTranslation findOrFail($id, $columns = ['*'])
 * @method static Builder|SymptomTranslation where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|SymptomTranslation whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder|SymptomTranslation orderBy($column, $direction = 'asc')
 * @method static Collection|SymptomTranslation[] get($columns = ['*'])
 * @method static Collection|SymptomTranslation[] all($columns = ['*'])
 */
class SymptomTranslation extends Model
{
	public $timestamps = false;
	protected $table = 'migraine_symptom_translations';
	protected $fillable = [
		'symptom_id',
		'locale',
		'name',
		'description'
	];
}
