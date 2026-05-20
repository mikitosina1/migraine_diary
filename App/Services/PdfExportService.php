<?php

namespace Modules\MigraineDiary\App\Services;

use TCPDF;

/**
 * Renders migraine report rows into a PDF binary string.
 */
class PdfExportService
{
	/**
	 * Generate a PDF document for the requested report period.
	 *
	 * @param list<array<string, mixed>> $rows Flat report rows.
	 * @param string $period Period label/key included in the PDF heading.
	 * @return string Raw PDF binary content.
	 */
	public function generate(array $rows, string $period): string
	{
		$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetCreator(config('app.name', 'Migraine Diary'));
		$pdf->SetTitle('Migraine Report');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(10, 10, 10);
		$pdf->SetAutoPageBreak(true, 10);
		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 9);

		$pdf->writeHTML($this->buildHtml($rows, $period), true, false, true, false, '');

		return $pdf->Output('', 'S');
	}

	/**
	 * Build escaped HTML consumed by TCPDF.
	 *
	 * @param list<array<string, mixed>> $rows Flat report rows.
	 * @param string $period Period label/key included in the report.
	 * @return string Safe HTML table markup.
	 */
	private function buildHtml(array $rows, string $period): string
	{
		$periodLabel = htmlspecialchars($period, ENT_QUOTES, 'UTF-8');
		$html = '<h2>Migraine Report</h2><p><strong>Period:</strong> ' . $periodLabel . '</p>';

		if ($rows === []) {
			return $html . '<p>No attacks recorded for this period.</p>';
		}

		$columns = ['Date', 'Pain Level', 'Duration', 'Symptoms', 'Triggers', 'Medications', 'Notes'];
		$html .= '<table border="1" cellpadding="4" cellspacing="0"><thead><tr>';

		foreach ($columns as $column) {
			$html .= '<th><strong>' . htmlspecialchars($column, ENT_QUOTES, 'UTF-8') . '</strong></th>';
		}

		$html .= '</tr></thead><tbody>';

		foreach ($rows as $row) {
			$html .= '<tr>';
			foreach ($columns as $column) {
				$value = (string) ($row[$column] ?? '');
				$html .= '<td>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</td>';
			}
			$html .= '</tr>';
		}

		$html .= '</tbody></table>';

		return $html;
	}
}
