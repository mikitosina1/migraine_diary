<?php

namespace Modules\MigraineDiary\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Models\Symptom;
use Modules\MigraineDiary\App\Models\Trigger;
use Modules\MigraineDiary\App\Models\Med;
use Modules\MigraineDiary\App\Http\Requests\StoreAttackRequest;
use Modules\MigraineDiary\App\Http\Requests\UpdateAttackRequest;
use Modules\MigraineDiary\App\Services\AttackService;
use Modules\MigraineDiary\App\Repositories\{
	UserSymptomRepository,
	UserTriggerRepository,
	UserMedRepository
};

/**
 * MigraineAttackController
 *
 * Controller for managing migraine attack records in the diary application.
 * Handles CRUD operations for migraine attacks including symptoms, triggers, and medications.
 * Provides both web views and AJAX endpoints for attack management.
 *
 * @package Modules\MigraineDiary\App\Http\Controllers
 */
class MigraineAttackController extends Controller
{
	public function __construct(
		private readonly AttackService $attackService,
		private readonly UserSymptomRepository $userSymptomRepo,
		private readonly UserTriggerRepository $userTriggerRepo,
		private readonly UserMedRepository $userMedRepo
	) {}

	public function index(): View
	{
		return view('migrainediary::index');
	}

	public function create(): View
	{
		return view('migrainediary::create');
	}

	public function store(StoreAttackRequest $request): JsonResponse
	{
		$attack = $this->attackService->createAttack(
			$request->validated(),
			auth()->id()
		);

		return response()->json([
			'success' => true,
			'message' => __('migrainediary::migraine_diary.add_success'),
			'attack_id' => $attack->id,
		]);
	}

	public function show(Attack $attack): View
	{
		return view('migrainediary::attacks.show', compact('attack'));
	}

	public function edit(Attack $attack): View
	{
		$userId = auth()->id();

		return view('migrainediary::user.attacks._form', [
			// Basic entities
			'symptoms' => Symptom::getListWithTranslations(),
			'triggers' => Trigger::getListWithTranslations(),
			'meds' => Med::getListWithTranslations(),

			// Custom entities
			'userSymptoms' => $this->userSymptomRepo->getForUser($userId),
			'userTriggers' => $this->userTriggerRepo->getForUser($userId),
			'userMeds' => $this->userMedRepo->getForUser($userId),

			'attack' => $attack,
			'mode' => 'edit',
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateAttackRequest $request, Attack $attack): JsonResponse
	{
		if ($attack->user_id !== auth()->id()) {
			abort(403, 'Unauthorized action.');
		}

		$this->attackService->updateAttack($attack, $request->validated());

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

	public function endAttackAjax(int $id): JsonResponse
	{
		$attack = $this->attackService->endAttack($id, auth()->id());

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
}
