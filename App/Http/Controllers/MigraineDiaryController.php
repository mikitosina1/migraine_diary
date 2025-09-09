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
use Modules\MigraineDiary\App\Models\MigraineUserMed;
use Modules\MigraineDiary\App\Models\MigraineUserSymptom;
use Modules\MigraineDiary\App\Models\MigraineUserTrigger;

class MigraineDiaryController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$validated = $request->validate([
			'range' => 'nullable|string|in:month,3months,year',
			'pain_level' => 'nullable|string|in:all,1,2,3,4,5,6,7,8,9,10'
		]);

		$range = $validated['range'] ?? 'year';
		$painLevel = $validated['pain_level'] ?? 'all';

		$attacks = $this->getFilteredAttacks($range, $painLevel);

		// For AJAX requests, return only the list partial
		if ($request->ajax()) {
			return view('migrainediary::components.attacks-list', [
				'attacks' => $attacks,
				'currentRange' => $range,
				'currentPainLevel' => $painLevel
			]);
		}

		// For full page requests
		return view('migrainediary::user.index', [
			'symptoms' => MigraineSymptom::getListWithTranslations(),
			'userSymptoms' => MigraineUserSymptom::getForUser(auth()->id()),
			'triggers' => MigraineTrigger::getListWithTranslations(),
			'userTriggers' => MigraineUserTrigger::getForUser(auth()->id()),
			'meds' => MigraineMed::getListWithTranslations(),
			'userMeds' => MigraineUserMed::getForUser(auth()->id()),
			'locales' => config('app.locales'),
			'attacks' => $attacks,
			'currentRange' => $range,
			'currentPainLevel' => $painLevel,
			'mode' => 'show',
		]);
	}

	/**
	 * Get filtered attacks based on range
	 */
	private function getFilteredAttacks(string $range, string $painLevel = 'all')
	{
		$query = MigraineAttack::where('user_id', auth()->id())
			->orderBy('start_time', 'desc');

		// Apply date range filter
		$startDate = match($range) {
			'month' => Carbon::now()->subMonth(),
			'3months' => Carbon::now()->subMonths(3),
			'year' => Carbon::now()->subYear()
		};

		$query->where('start_time', '>=', $startDate);

		// Apply pain level filter only if not 'all'
		if ($painLevel !== 'all') {
			$query->where('pain_level', (int)$painLevel);
		}

		return $query->get();
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
