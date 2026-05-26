<?php

use Illuminate\Support\Facades\Route;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\Admin\DictionaryController as AdminDictionaryController;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\AttackController;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\DashboardController;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\DictionaryController as UserDictionaryController;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\ReportController;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\StatisticController;
use Modules\MigraineDiary\App\Http\Controllers\Api\V1\User\TranslationController;

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

		Route::get('/dictionaries', UserDictionaryController::class)
			->name('dictionaries.index');

		Route::get('/statistics', StatisticController::class)
			->name('statistics.index');

		Route::post('/reports/email', [ReportController::class, 'sendEmail'])
			->name('reports.email');

		Route::post('/reports/excel', [ReportController::class, 'downloadExcel'])
			->name('reports.excel');

		Route::post('/reports/pdf', [ReportController::class, 'downloadPdf'])
			->name('reports.pdf');

		Route::get('/translations', TranslationController::class)
			->name('translations.index');
	});

Route::prefix('v1/admin/migraine-diary')
	->middleware(['auth:sanctum', 'is_admin'])
	->name('api.v1.admin.migraine-diary.')
	->group(function () {
		Route::get('/dictionaries', [AdminDictionaryController::class, 'indexAll'])
			->name('dictionaries.index-all');

		Route::prefix('dictionaries/{type}')
			->whereIn('type', ['symptoms', 'triggers', 'meds'])
			->name('dictionaries.')
			->group(function () {
				Route::get('/', [AdminDictionaryController::class, 'index'])
					->name('index');
				Route::post('/', [AdminDictionaryController::class, 'store'])
					->name('store');
				Route::get('/{id}', [AdminDictionaryController::class, 'show'])
					->whereNumber('id')->name('show');
				Route::patch('/{id}', [AdminDictionaryController::class, 'patch'])
					->whereNumber('id')->name('patch');
				Route::delete('/{id}', [AdminDictionaryController::class, 'destroy'])
					->whereNumber('id')->name('destroy');
			});
	});
