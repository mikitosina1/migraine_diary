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
		public string $period
	) {}

	public function build()
	{
		return $this->subject($this->getSubject())
			->view($this->template)
			->with([
				'data' => $this->reportData,
				'period' => $this->period,
				'user' => auth()->user(),
			]);
	}

	public function attachExcel(array $reportData)
	{
		$fileName = 'migraine-report-' . now()->format('Y-m-d') . '.xlsx';

		$this->attach(
			Excel::raw(new ExcelExportService($reportData), \Maatwebsite\Excel\Excel::XLSX),
			$fileName
		);

		return $this;
	}

	protected function getSubject(): string
	{
		return trans('migrainediary::emails.subject', [
			'date' => now()->format('d.m.Y')
		]);
	}
}
