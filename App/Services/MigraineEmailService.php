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
	 *
	 * @return void
	 */
	public function sendReport(User $user, array $data): void
	{
		$reportData = app(MigraineExportService::class)->prepareData($user, $data['period']);

		$recipientEmail = $data['recipient_type'] === 'doctor' ? $data['doctor_email'] : $user->email;

		$template = $this->getTemplateByRecipientType($data['recipient_type']);

		$dateService = new DateRangeService();
		$range = $dateService->getRange($data['period']);
		$range = $range[0] . ' - ' . $range[1];

		$mailable = new MigraineReportMailable($reportData, $template, $range, $data['user_name'], $data['user_lastname']);

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
