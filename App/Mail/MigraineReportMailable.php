<?php

namespace Modules\MigraineDiary\App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MigraineDiary\App\Services\ExcelExportService;

class MigraineReportMailable extends Mailable
{
	use Queueable, SerializesModels;

	public function __construct(
		public array $reportData,
		public string $template,
		public string $period,
		public string $userName,
		public string $userLastname
	) {}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build(): MigraineReportMailable
	{
		return $this->subject($this->getSubject())
			->view($this->template)
			->with([
				'data' => $this->reportData,
				'period' => $this->period,
				'user' => auth()->user(),
				'userName' => $this->userName ?? auth()->user()->name,
				'userLastname' => $this->userLastname ?? auth()->user()->lastname,
			]);
	}

	/**
	 * Attach an Excel report to the email
	 *
	 * @param array $reportData
	 * @return $this
	 */
	public function attachExcel(array $reportData): static
	{
		$this->attachData(
			Excel::raw(new ExcelExportService($reportData), \Maatwebsite\Excel\Excel::XLSX),
			'migraine-report-' . now()->format('YmHdms') . '.xlsx'
		);

		return $this;
	}

	/**
	 * Get the email subject
	 *
	 * @return string
	 */
	protected function getSubject(): string
	{
		return trans('migrainediary::emails.subject', [
			'date' => $this->period
		]);
	}
}
