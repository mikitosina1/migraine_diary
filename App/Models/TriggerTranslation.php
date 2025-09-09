<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * TriggerTranslation Model
 *
 * Stores translated names and descriptions for predefined triggers.
 * Supports multiple languages for internationalization.
 *
 * @package Modules\MigraineDiary\App\Models
 *
 * @method static Builder|TriggerTranslation create(array $attributes = [])
 * @method static Builder|TriggerTranslation find($id, $columns = ['*'])
 * @method static Builder|TriggerTranslation findOrFail($id, $columns = ['*'])
 * @method static Builder|TriggerTranslation where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|TriggerTranslation whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder|TriggerTranslation orderBy($column, $direction = 'asc')
 * @method static Collection|TriggerTranslation[] get($columns = ['*'])
 * @method static Collection|TriggerTranslation[] all($columns = ['*'])
 */
class TriggerTranslation extends Model
{
	public $timestamps = false;
	protected $table = 'migraine_trigger_translations';
	protected $fillable = [
		'trigger_id',
		'locale',
		'name',
		'description'
	];
}
