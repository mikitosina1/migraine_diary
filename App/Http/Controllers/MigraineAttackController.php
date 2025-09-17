<?php

namespace Modules\MigraineDiary\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Models\Med;
use Modules\MigraineDiary\App\Models\Symptom;
use Modules\MigraineDiary\App\Models\Trigger;
use Modules\MigraineDiary\App\Models\UserMed;
use Modules\MigraineDiary\App\Models\UserSymptom;
use Modules\MigraineDiary\App\Models\UserTrigger;

/**
 * TODO: reformat code.
 */
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
		$validated = $this->getValid($request);

		$newSymptomsIds = [];
		foreach ($validated['userSymptomsNew'] ?? [] as $symptomName) {
			$symptom = UserSymptom::firstOrCreate(
				['user_id' => auth()->id(), 'name' => $symptomName],
				['name' => $symptomName]
			);
			$newSymptomsIds[] = $symptom->id;
		}

		$allUserSymptoms = array_merge(
			$validated['userSymptoms'] ?? [],
			$newSymptomsIds
		);

		$newTriggersIds = [];
		foreach ($validated['userTriggersNew'] ?? [] as $triggerName) {
			$trigger = UserTrigger::firstOrCreate(
				['user_id' => auth()->id(), 'name' => $triggerName],
				['name' => $triggerName]
			);
			$newTriggersIds[] = $trigger->id;
		}

		$allUserTriggers = array_merge(
			$validated['userTriggers'] ?? [],
			$newTriggersIds
		);

		$newMedsData = [];
		foreach ($validated['userMedsNew'] ?? [] as $medName) {
			$med = UserMed::firstOrCreate(
				['user_id' => auth()->id(), 'name' => $medName],
				['name' => $medName]
			);
			$newMedsData[$med->id] = ['dosage' => null];
		}

		$allUserMeds = array_merge(
			$validated['userMeds'] ?? [],
				$newMedsData
		);

		$attack = Attack::create([
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
		$attack->userSymptoms()->sync(array_map('intval', $allUserSymptoms));
		$attack->triggers()->sync(array_map('intval', $validated['triggers']));
		$attack->userTriggers()->sync(array_map('intval', $allUserTriggers));
		$attack->meds()->sync($meds);
		$attack->userMeds()->sync($allUserMeds);

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
	public function show(Attack $attack)
	{
		return view('migrainediary::attacks.show', compact('attack'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Attack $attack): View
	{
		$userId = auth()->id();

		return view('migrainediary::user.attacks._form', [
			'symptoms' => Symptom::getListWithTranslations(),
			'userSymptoms' => UserSymptom::getForUser($userId),
			'triggers' => Trigger::getListWithTranslations(),
			'userTriggers' => UserTrigger::getForUser($userId),
			'meds' => Med::getListWithTranslations(),
			'userMeds' => UserMed::getForUser($userId),
			'attack' => $attack,
			'mode' => 'edit',
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Attack $attack): JsonResponse
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
	public function destroy(Attack $attack)
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

	/**
	 * end attack
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function endAttack(int $id): RedirectResponse
	{
		$attack = Attack::where('user_id', auth()->id())
			->whereNull('end_time')
			->findOrFail($id);

		$attack->end_time = now();
		$attack->save();

		return back()->with('success', __('migrainediary::migraine_diary.attack_ended'));
	}

	/**
	 * end attack method for ajax
	 * @param int $id
	 * @return JsonResponse
	 */
	public function endAttackAjax(int $id): JsonResponse
	{
		$attack = Attack::where('user_id', auth()->id())
			->whereNull('end_time')
			->findOrFail($id);

		$attack->end_time = now();
		$attack->save();

		return response()->json([
			'success' => true,
			'message' => __('migrainediary::migraine_diary.attack_ended'),
			'attack' => [
				'id' => $attack->id,
				'end_time' => $attack->end_time->format('Y-m-d H:i:s'),
				'end_time_formatted' => $attack->end_time->format('d.m.Y H:i')
			]
		]);
	}

	protected function getValid($request): array
	{
		return $request->validate([
			'start_time'         => 'required|date',
			'pain_level'         => 'required|integer|min:1|max:10',
			'notes'              => 'nullable|string',
			'symptoms'           => 'array',
			'symptoms.*'         => 'integer|exists:migraine_symptoms,id',
			'userSymptoms'       => 'array',
			'userSymptoms.*'     => 'integer|exists:migraine_user_symptoms,id',
			'userSymptomsNew'    => 'array',
			'userSymptomsNew.*'  => 'string|distinct|max:255',
			'meds'               => 'array',
			'meds.*.id'          => 'required|integer|exists:migraine_meds,id',
			'meds.*.dosage'      => 'nullable|string',
			'userMeds'           => 'array',
			'userMeds.*'         => 'integer|exists:migraine_user_meds,id',
			'userMedsNew'        => 'array',
			'userMedsNew.*'      => 'string|distinct|max:255',
			'triggers'           => 'array',
			'triggers.*'         => 'integer|exists:migraine_triggers,id',
			'userTriggers'       => 'array',
			'userTriggers.*'     => 'integer|exists:migraine_user_triggers,id',
			'userTriggersNew'    => 'array',
			'userTriggersNew.*'  => 'string|distinct|max:255',
		]);
	}

}
