<?php

namespace Modules\MigraineDiary\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\MigraineDiary\App\Http\Requests\AttackFilterRequest;
use Modules\MigraineDiary\App\Repositories\AttackListRepository;
use Modules\MigraineDiary\App\Services\AttackFilterService;

class MigraineDiaryController extends Controller
{
	public function __construct(
		private readonly AttackFilterService $filterService,
		private readonly AttackListRepository $listRepository
	) {}
	/**
	 * Display a listing of the resource.
	 */
	public function index(AttackFilterRequest $request)
	{
		$range = $request->getRange();
		$painLevel = $request->getPainLevel();
		$attacks = $this->filterService->getFilteredAttacks($range, $painLevel);

		// For AJAX requests, return only the list partial
		if ($request->ajax()) {
			return view('migrainediary::components.attacks-list', compact('attacks', 'range', 'painLevel'));
		}

		// For full page requests
		return view('migrainediary::user.index', array_merge($this->listRepository->getEntities(auth()->id()), [
			'locales' => config('app.locales'),
			'attacks' => $attacks,
			'currentRange' => $range,
			'currentPainLevel' => $painLevel,
			'mode' => 'show',
		]));
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
//		return view('migrainediary::create');
	}

	/**
	 * Show the specified resource.
	 */
	public function show($id)
	{
//		return view('migrainediary::show');
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit($id)
	{
//		return view('migrainediary::edit');
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

		if (!in_array($locale, ['en', 'de', 'ru'])) {
			$locale = 'en'; // basic locale fallback
		}

		$translations = trans('migrainediary::migraine_diary', [], $locale);

		return response()->json([
			'success' => true,
			'translations' => $translations
		]);
	}

}
