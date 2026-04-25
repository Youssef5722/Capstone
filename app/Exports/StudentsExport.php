<?php

namespace App\Exports;

use App\Models\Student;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentsExport
{
    public function __construct(
        private readonly int $levelId,
        private readonly int $academicYearId,
    ) {}

    /**
     * Stream an xlsx file directly to the browser.
     * Uses PhpSpreadsheet directly since maatwebsite/excel is not compatible with PHP 8.5.
     */
    public function download(string $filename = 'students.xlsx'): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $students = Student::where('level_id', $this->levelId)
                           ->where('academic_year_id', $this->academicYearId)
                           ->orderBy('name')
                           ->get()
                           ->makeVisible('activation_code');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Students');

        // ── Header row ────────────────────────────────────────────────
        $sheet->fromArray(['Name', 'University ID', 'Activation Code', 'Status'], null, 'A1');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // ── Data rows ─────────────────────────────────────────────────
        $row = 2;
        foreach ($students as $student) {
            $sheet->fromArray([
                $student->name,
                $student->university_id,
                $student->activation_code,
                $student->is_active ? 'Activated' : 'Pending',
            ], null, "A{$row}");
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
