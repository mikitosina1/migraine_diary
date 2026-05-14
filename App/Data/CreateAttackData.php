<?php

namespace Modules\MigraineDiary\App\Data;

/**
 * DTO for migraine attack creation payload.
 *
 * Transfers validated attack data between controller and service layer.
 */
class CreateAttackData
{
	/**
	 * @param  string  $startTime  Attack start timestamp (validated format).
	 * @param  int  $painLevel  Pain intensity scale value.
	 * @param  ?string  $notes  Optional free-text notes.
	 * @param  array<int>  $symptoms  Predefined symptom IDs.
	 * @param  array<int>  $userSymptoms  Existing custom symptom IDs.
	 * @param  array<string>  $userSymptomsNew  New custom symptom names.
	 * @param  array<int>  $triggers  Predefined trigger IDs.
	 * @param  array<int>  $userTriggers  Existing custom trigger IDs.
	 * @param  array<string>  $userTriggersNew  New custom trigger names.
	 * @param  array<int>  $meds  Predefined medication IDs.
	 * @param  array<int>  $userMeds  Existing custom medication IDs.
	 * @param  array<string>  $userMedsNew  New custom medication names.
	 */
	public function __construct(
		public readonly string $startTime,
		public readonly int $painLevel,
		public readonly ?string $notes,
		public readonly array $symptoms,
		public readonly array $userSymptoms,
		public readonly array $userSymptomsNew,
		public readonly array $triggers,
		public readonly array $userTriggers,
		public readonly array $userTriggersNew,
		public readonly array $meds,
		public readonly array $userMeds,
		public readonly array $userMedsNew,
	)
	{}

	/**
	 * Create DTO from validated request payload.
	 *
	 * @param array{
	 *     start_time: string,
	 *     pain_level: int,
	 *     notes: ?string,
	 *     symptoms: array<int>,
	 *     userSymptoms: array<int>,
	 *     userSymptomsNew: array<string>,
	 *     triggers: array<int>,
	 *     userTriggers: array<int>,
	 *     userTriggersNew: array<string>,
	 *     meds: array<int>,
	 *     userMeds: array<int>,
	 *     userMedsNew: array<string>
	 * } $data
	 *
	 * @return self
	 */
	public static function fromArray(array $data): self
	{
		return new self(
			startTime: $data['start_time'],
			painLevel: (int) $data['pain_level'],
			notes: $data['notes'],
			symptoms: $data['symptoms'],
			userSymptoms: $data['userSymptoms'],
			userSymptomsNew: $data['userSymptomsNew'],
			triggers: $data['triggers'],
			userTriggers: $data['userTriggers'],
			userTriggersNew: $data['userTriggersNew'],
			meds: $data['meds'],
			userMeds: $data['userMeds'],
			userMedsNew: $data['userMedsNew'],
		);
	}

	/**
	 * Convert DTO into service payload.
	 *
	 * @return array{
	 *     start_time: string,
	 *     pain_level: int,
	 *     notes: ?string,
	 *     symptoms: array<int>,
	 *     userSymptoms: array<int>,
	 *     userSymptomsNew: array<string>,
	 *     triggers: array<int>,
	 *     userTriggers: array<int>,
	 *     userTriggersNew: array<string>,
	 *     meds: array<int>,
	 *     userMeds: array<int>,
	 *     userMedsNew: array<string>
	 * }
	 */
	public function toServiceArray(): array
	{
		return [
			'start_time' => $this->startTime,
			'pain_level' => $this->painLevel,
			'notes' => $this->notes,
			'symptoms' => $this->symptoms,
			'userSymptoms' => $this->userSymptoms,
			'userSymptomsNew' => $this->userSymptomsNew,
			'triggers' => $this->triggers,
			'userTriggers' => $this->userTriggers,
			'userTriggersNew' => $this->userTriggersNew,
			'meds' => $this->meds,
			'userMeds' => $this->userMeds,
			'userMedsNew' => $this->userMedsNew,
		];
	}
}
