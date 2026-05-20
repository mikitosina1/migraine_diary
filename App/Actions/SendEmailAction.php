<?php

namespace Modules\MigraineDiary\App\Actions;

use App\Models\User;
use Modules\MigraineDiary\App\Data\SendReportEmailData;
use Modules\MigraineDiary\App\Services\MigraineEmailService;

/**
 * Sends a migraine report to the user or their doctor by email.
 */
class SendEmailAction
{
	/**
	 * @param MigraineEmailService $email Service responsible for building and sending report emails.
	 */
	public function __construct(
		private readonly MigraineEmailService $email,
	) {}

	/**
	 * Send the report according to validated email delivery options.
	 *
	 * @param User $user Report owner.
	 * @param SendReportEmailData $data Email delivery options.
	 * @return void
	 */
	public function execute(User $user, SendReportEmailData $data): void
	{
		$this->email->sendReport($user, $data->toServiceArray());
	}
}
