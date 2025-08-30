<?php

namespace Modules\MigraineDiary\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Modules\MigraineDiary\App\Models\MigraineAttack;
use Modules\MigraineDiary\App\Models\MigraineMed;
use Modules\MigraineDiary\App\Models\MigraineSymptom;
use Modules\MigraineDiary\App\Models\MigraineTrigger;

class MigraineAttackController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): View|Application|Factory
	{
		return view('migrainediary::index');
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse
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
			'message'   => __('migrainediary::migraine_diary.add_success'),
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
	public function show(MigraineAttack $attack)
	{
		return view('migrainediary::attacks.show', compact('attack'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(MigraineAttack $attack): View
	{
		return view('migrainediary::user.attacks._form', [
			'symptoms' => MigraineSymptom::getListWithTranslations(),
			'triggers' => MigraineTrigger::getListWithTranslations(),
			'meds' => MigraineMed::getListWithTranslations(),
			'attack' => $attack,
			'mode' => 'edit',
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, MigraineAttack $attack): JsonResponse
	{
		if ($attack->user_id !== auth()->id()) {
			abort(403, 'Unauthorized action.');
		}

		$validated = $request->validate([
			'start_time'    => 'required|date',
			'end_time'      => 'nullable|date|after_or_equal:start_time',
			'pain_level'    => 'required|integer|min:1|max:10',
			'notes'         => 'nullable|string',
			'symptoms'      => 'array',
			'symptoms.*'    => 'integer|exists:migraine_symptoms,id',
			'meds'          => 'array',
			'meds.*.id'     => 'required|integer|exists:migraine_meds,id',
			'meds.*.dosage' => 'nullable|string',
			'triggers'      => 'array',
			'triggers.*'    => 'integer|exists:migraine_triggers,id',
		]);

		$attack->update([
			'start_time' => $validated['start_time'],
			'end_time'   => $validated['end_time'] ?? null,
			'pain_level' => $validated['pain_level'],
			'notes'      => $validated['notes'] ?? null,
		]);

		$attack->symptoms()->sync(array_map('intval', $validated['symptoms'] ?? []));
		$attack->triggers()->sync(array_map('intval', $validated['triggers'] ?? []));

		$meds = [];
		if (!empty($validated['meds'])) {
			foreach ($validated['meds'] as $med) {
				$meds[$med['id']] = ['dosage' => $med['dosage'] ?? null];
			}
		}
		$attack->meds()->sync($meds);

		return response()->json([
			'success' => true,
			'message' => 'Attack updated successfully!',
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(MigraineAttack $attack)
	{
		if ($attack->user_id !== auth()->id()) {
			abort(403, 'Unauthorized action.');
		}

		$attack->delete();

		return response()->json([
			'success' => true,
			'message' => 'Attack deleted successfully!',
		]);
	}
}
