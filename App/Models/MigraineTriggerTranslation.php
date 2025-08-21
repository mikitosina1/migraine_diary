<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Model;

class MigraineTriggerTranslation extends Model
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
