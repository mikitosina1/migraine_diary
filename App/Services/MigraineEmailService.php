<?php

namespace Modules\MigraineDiary\App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Modules\MigraineDiary\App\Mail\MigraineReportMailable;

class MigraineEmailService
{
	/**
	 * Send report email to user or doctor
	 *
	 * @param mixed $user
	 * @param array $data
	 * @param string $recipientType
	 * @param string|null $doctorEmail
	 * @return void
	 */
	public function sendReport(User $user, array $data, string $recipientType, string $doctorEmail = null): void
	{
		$reportData = app(MigraineExportService::class)->prepareData($user, $data['period']);

		$recipientEmail = $recipientType === 'doctor' ? $doctorEmail : $user->email;

		$template = $this->getTemplateByRecipientType($recipientType);

		$mailable = new MigraineReportMailable($reportData, $template, $data['period']);

		$mailable->attachExcel($reportData);

		Mail::to($recipientEmail)->send($mailable);
	}

	protected function getTemplateByRecipientType(string $recipientType): string
	{
		return match($recipientType) {
			'doctor' => 'migrainediary::emails.doctor_report',
			default => 'migrainediary::emails.personal_report',
		};
	}
}
