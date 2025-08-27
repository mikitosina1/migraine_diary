<?php

namespace Modules\MigraineDiary\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\MigraineDiary\App\Models\MigraineAttack;
use Modules\MigraineDiary\App\Models\MigraineMed;
use Modules\MigraineDiary\App\Models\MigraineSymptom;
use Modules\MigraineDiary\App\Models\MigraineTrigger;

class MigraineDiaryController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$validated = $request->validate([
			'range' => 'nullable|string|in:month,3months,year'
		]);

		$range = $validated['range'] ?? 'year';

		return view('migrainediary::user.index', [
			'symptoms' => MigraineSymptom::getListWithTranslations(),
			'triggers' => MigraineTrigger::getListWithTranslations(),
			'meds' => MigraineMed::getListWithTranslations(),
			'locales' => config('app.locales'),
			'attacks' => $this->getFilteredAttacks($range),
			'currentRange' => $range,
		]);
	}

	/**
	 * Get filtered attacks based on range
	 */
	private function getFilteredAttacks(string $range)
	{
		$query = MigraineAttack::where('user_id', auth()->id())
			->orderBy('start_time', 'desc');

		$startDate = match($range) {
			'month' => Carbon::now()->subMonth(),
			'3months' => Carbon::now()->subMonths(3),
			'year' => Carbon::now()->subYear(),
		};

		return $query->where('start_time', '>=', $startDate)->get();
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		return view('migrainediary::create');
	}

	/**
	 * Show the specified resource.
	 */
	public function show($id)
	{
		return view('migrainediary::show');
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit($id)
	{
		return view('migrainediary::edit');
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Request $request)
	{
		//
	}

	public function getTranslations(Request $request): JsonResponse
	{
		$locale = $request->header('Accept-Language') ?? app()->getLocale();
		$locale = substr($locale, 0, 2);

		$availableLocales = ['en', 'de', 'ru'];
		if (!in_array($locale, $availableLocales)) {
			$locale = 'en'; // basic translation
		}

		$translations = trans('migrainediary::migraine_diary', [], $locale);

		return response()->json([
			'success' => true,
			'translations' => $translations
		]);
	}

}
