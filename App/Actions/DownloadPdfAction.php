<?php

namespace Modules\MigraineDiary\App\Actions;

use App\Models\User;
use Illuminate\Http\Response;
use Modules\MigraineDiary\App\Data\ReportExportData;
use Modules\MigraineDiary\App\Services\MigraineExportService;
use Modules\MigraineDiary\App\Services\PdfExportService;

/**
 * Builds and returns a PDF download for the user's migraine report.
 */
class DownloadPdfAction
{
	/**
	 * @param MigraineExportService $export Service preparing report rows.
	 * @param PdfExportService $pdfExport Service rendering report rows into PDF content.
	 */
	public function __construct(
		private readonly MigraineExportService $export,
		private readonly PdfExportService $pdfExport,
	) {}

	/**
	 * Build a PDF download response for the requested report period.
	 *
	 * @param User $user Report owner.
	 * @param ReportExportData $data Export options.
	 * @return Response
	 */
	public function execute(User $user, ReportExportData $data): Response
	{
		$rows = $this->export->prepareData($user, $data->period);
		$content = $this->pdfExport->generate($rows, $data->period);
		$filename = 'migraine-report-' . now()->format('Y-m-d_His') . '.pdf';

		return response($content, 200, [
			'Content-Type' => 'application/pdf',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		]);
	}
}
