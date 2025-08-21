<?php

namespace Modules\MigraineDiary\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
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
	public function index()
	{
		return view('migrainediary::user.index', [
			'symptoms' => MigraineSymptom::getListWithTranslations(),
			'triggers' => MigraineTrigger::getListWithTranslations(),
			'meds' => MigraineMed::getListWithTranslations(),
			'locales' => config('app.locales'),
			'attacks' => MigraineAttack::getForUser(auth()->id()),
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$validated = $request->validate([
			'start_time' => 'required|date',
			'pain_level' => 'required|integer|min:1|max:10',
			'notes'      => 'nullable|string',
			'symptoms'   => 'array',
			'symptoms.*' => 'integer|exists:migraine_symptoms,id',
			'meds'       => 'array',
			'meds.*.id'  => 'required|integer|exists:migraine_meds,id',
			'meds.*.dosage' => 'nullable|string',
			'triggers'   => 'array',
			'triggers.*' => 'integer|exists:migraine_triggers,id',
		]);

		$attack = MigraineAttack::create([
			'user_id'    => auth()->id(),
			'start_time' => $validated['start_time'],
			'pain_level' => $validated['pain_level'],
			'notes'      => $validated['notes'] ?? null,
		]);

		$meds = [];
		if (!empty($validated['meds'])) {
			foreach ($validated['meds'] as $med) {
				$meds[$med['id']] = ['dosage' => $med['dosage'] ?? null];
			}
		}

		$attack->symptoms()->sync(array_map('intval', $validated['symptoms']));
		$attack->triggers()->sync(array_map('intval', $validated['triggers']));
		$attack->meds()->sync($meds);

		return response()->json([
			'success'   => true,
			'message'   => 'Attack created successfully!',
			'attack_id' => $attack->id,
		]);
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
	public function update(Request $request, $id): RedirectResponse
	{
		return redirect()->route('welcome')->with('success', 'we2e');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy($id)
	{
		//
	}
}
