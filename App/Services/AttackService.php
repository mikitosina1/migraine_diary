<?php

namespace Modules\MigraineDiary\App\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Repositories\{
	AttackRepository,
	UserSymptomRepository,
	UserTriggerRepository,
	UserMedRepository
};

/**
 * AttackService
 *
 * Service for managing migraine attacks.
 * Handles the creation, updating, and completion of attacks,
 * as well as the synchronization of related data (symptoms, triggers, medications).
 *
 * @package Modules\MigraineDiary\App\Services
 */
class AttackService
{
	/**
	 * Constructor
	 *
	 * @param AttackRepository $attackRepository Attack repository
	 * @param UserSymptomRepository $userSymptomRepository Repository for user symptoms
	 * @param UserTriggerRepository $userTriggerRepository Repository for user triggers
	 * @param UserMedRepository $userMedRepository Repository for user medications
	 */
	public function __construct(
		private readonly AttackRepository      $attackRepository,
		private readonly UserSymptomRepository $userSymptomRepository,
		private readonly UserTriggerRepository $userTriggerRepository,
		private readonly UserMedRepository     $userMedRepository
	) {}

	/**
	 * Creates a new migraine attack
	 *
	 * @param array $data Attack data:
	 *   - start_time: attack start time
	 *   - pain_level: pain level (1-10)
	 *   - notes: notes (optional)
	 *   - symptoms: array of symptom IDs (optional)
	 *   - userSymptoms: array of user symptom IDs (optional)
	 *   - userSymptomsNew: array of new user symptoms (optional)
	 *   - triggers: array of trigger IDs (optional)
	 *   - userTriggers: array of user trigger IDs (optional)
	 *   - userTriggersNew: array of new user triggers (optional)
	 *   - meds: array of medications with dosage (optional)
	 *   - userMeds: array of user medication IDs (optional)
	 *   - userMedsNew: array of new user medications (optional)
	 * @param int $userId User ID
	 * @return Attack Created attack
	 */
	public function createAttack(array $data, int $userId): Attack
	{
		$attack = $this->attackRepository->create([
			'user_id' => $userId,
			'start_time' => $data['start_time'],
			'pain_level' => $data['pain_level'],
			'notes' => $data['notes'] ?? null,
		]);

		$this->syncRelations($attack, $data);

		return $attack;
	}

	/**
	 * Synchronizes related attack data (symptoms, triggers, medications)
	 *
	 * Processes both predefined and user-defined records.
	 * Creates new user entries if necessary.
	 *
	 * @param Attack $attack Attack to synchronize
	 * @param array $data Data to synchronise:
	 *   - symptoms: array of preset symptom IDs
	 *   - userSymptoms: array of preset trigger IDs
	 *   - userSymptomsNew: array of new user symptoms to create
	 *   - triggers: array of predefined trigger IDs
	 *   - userTriggers: array of user trigger IDs
	 *   - userTriggersNew: array of new user triggers to create
	 *   - meds: array of medications with dosage
	 *   - userMeds: array of user medication IDs
	 *   - userMedsNew: array of new user medications to create
	 * @return void
	 */
	private function syncRelations(Attack $attack, array $data): void
	{
		// Symptoms
		$userSymptoms = $this->userSymptomRepository->processUserSymptoms(
			$data['userSymptoms'] ?? [],
			$data['userSymptomsNew'] ?? [],
			$attack->user_id
		);
		$attack->symptoms()->sync($data['symptoms'] ?? []);
		$attack->userSymptoms()->sync($userSymptoms);

		// Triggers
		$userTriggers = $this->userTriggerRepository->processUserTriggers(
			$data['userTriggers'] ?? [],
			$data['userTriggersNew'] ?? [],
			$attack->user_id
		);
		$attack->triggers()->sync($data['triggers'] ?? []);
		$attack->userTriggers()->sync($userTriggers);

		// Meds
		$userMeds = $this->userMedRepository->processUserMeds(
			$data['userMeds'] ?? [],
			$data['userMedsNew'] ?? [],
			$attack->user_id
		);
		$meds = $this->prepareMedsDosage($data['meds'] ?? []);
		$attack->meds()->sync($meds);
		$attack->userMeds()->sync($userMeds);
	}

	/**
	 * Prepares medication data with dosage for synchronization
	 *
	 * Converts the medication array into a format suitable for many-to-many relationships
	 * with additional data (pivot) in the form of dosage.
	 *
	 * @param array $meds Medication data:
	 *   - id: ID of medication
	 *   - dosage: dosage of medication (optional)
	 * @return array
	 */
	private function prepareMedsDosage(array $meds): array
	{
		$result = [];
		foreach ($meds as $med) {
			$result[$med['id']] = ['dosage' => $med['dosage'] ?? null];
		}
		return $result;
	}

	/**
	 * Updates an existing migraine attack
	 *
	 * @param Attack $attack Attack to update
	 * @param array $data New attack data:
	 *   - start_time: attack start time
	 *   - end_time: attack end time (optional)
	 *   - pain_level: pain level (1-10)
	 *   - notes: notes (optional)
	 *   - symptoms: array of symptom IDs (optional)
	 *   - userSymptoms: array of user symptom IDs (optional)
	 *   - userSymptomsNew: array of new user symptoms (optional)
	 *   - triggers: array of trigger IDs (optional)
	 *   - userTriggers: array of user trigger IDs (optional)
	 *   - userTriggersNew: array of new user triggers (optional)
	 *   - meds: array of medications with dosage (optional)
	 *   - userMeds: array of user medication IDs (optional)
	 *   - userMedsNew: array of new user medications (optional)
	 * @return void
	 */
	public function updateAttack(Attack $attack, array $data): void
	{
		$this->attackRepository->update($attack, [
			'start_time' => $data['start_time'],
			'end_time' => $data['end_time'] ?? null,
			'pain_level' => $data['pain_level'],
			'notes' => $data['notes'] ?? null,
		]);

		$this->syncRelations($attack, $data);
	}

	/**
	 * Ends an active migraine attack by setting the end time
	 *
	 * @param int $id ID of attack
	 * @param int $userId user ID
	 *
	 * @return Attack
	 *
	 * @throws ModelNotFoundException
	 */
	public function endAttack(int $id, int $userId): Attack
	{
		$attack = $this->attackRepository->findOrFailForUser($id, $userId);
		$this->attackRepository->endAttack($attack);
		return $attack;
	}
}
