<?php

use Illuminate\Support\Facades\Route;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\AttackController;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\DashboardController;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\DictionaryController;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\StatisticController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::prefix('v1/migraine-diary')
	->middleware('auth:sanctum')
	->name('api.v1.migraine-diary.')
	->group(function () {
		Route::get('/dashboard', DashboardController::class)
			->name('dashboard');

		Route::get('/attacks/active', [AttackController::class, 'active'])
			->name('attacks.active');

		Route::apiResource('/attacks', AttackController::class);

		Route::post('/attacks/{attack}/end', [AttackController::class, 'end'])
			->name('attacks.end');

		Route::get('/dictionaries', DictionaryController::class)
			->name('dictionaries.index');

		Route::get('/statistics', StatisticController::class)
			->name('statistics.index');
	});
