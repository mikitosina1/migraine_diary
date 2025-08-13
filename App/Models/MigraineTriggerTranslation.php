<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Model;

class MigraineTriggerTranslation extends Model
{
	protected $table = 'migraine_trigger_translations';

	protected $fillable = [
		'trigger_id',
		'locale',
		'name',
		'description'
	];

	public $timestamps = false;
}
