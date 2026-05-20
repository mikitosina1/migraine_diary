<?php

namespace Modules\MigraineDiary\App\Data;

/**
 * DTO for sending a migraine report by email.
 *
 * Carries validated request data from the HTTP layer into the email report
 * use case without exposing the raw request object to services.
 */
class SendReportEmailData
{
	/**
	 * @param string $recipientType Recipient target: self or doctor.
	 * @param string $period Supported period key: month, 3months, or year.
	 * @param string|null $doctorEmail Required when recipient type is doctor.
	 * @param string|null $userName Optional name override for report output.
	 * @param string|null $userLastname Optional lastname override for report output.
	 * @param list<string> $formats Requested attachment formats.
	 */
	public function __construct(
		public readonly string $recipientType,
		public readonly string $period,
		public readonly ?string $doctorEmail,
		public readonly ?string $userName,
		public readonly ?string $userLastname,
		public readonly array $formats,
	) {}

	/**
	 * Create DTO from validated request payload.
	 *
	 * @param array{
	 *     recipient_type: string,
	 *     period: string,
	 *     doctor_email?: ?string,
	 *     user_name?: ?string,
	 *     user_lastname?: ?string,
	 *     formats?: ?array<int, string>
	 * } $data
	 * @return self
	 */
	public static function fromValidated(array $data): self
	{
		return new self(
			recipientType: $data['recipient_type'],
			period: $data['period'],
			doctorEmail: $data['doctor_email'] ?? null,
			userName: $data['user_name'] ?? null,
			userLastname: $data['user_lastname'] ?? null,
			formats: $data['formats'] ?? ['pdf', 'excel'],
		);
	}

	/**
	 * Convert DTO into the legacy email service payload shape.
	 *
	 * @return array{
	 *     recipient_type: string,
	 *     period: string,
	 *     doctor_email: ?string,
	 *     user_name: ?string,
	 *     user_lastname: ?string,
	 *     formats: list<string>
	 * }
	 */
	public function toServiceArray(): array
	{
		return [
			'recipient_type' => $this->recipientType,
			'period' => $this->period,
			'doctor_email' => $this->doctorEmail,
			'user_name' => $this->userName,
			'user_lastname' => $this->userLastname,
			'formats' => $this->formats,
		];
	}
}
