<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles
{
    protected $attendances;
    protected $isIndividualExport;

    public function __construct($attendances, $isIndividualExport = false)
    {
        $this->attendances = $attendances;
        $this->isIndividualExport = $isIndividualExport;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $attendancesData = $this->formatAttendances();

        // Jika ekspor individu, tambahkan total jam kerja
        if ($this->isIndividualExport) {
            $attendancesData->push($this->getTotalHoursRow());
        }

        return $attendancesData;
    }

    /**
     * Format data attendances.
     *
     * @return \Illuminate\Support\Collection
     */
    private function formatAttendances()
    {
        return $this->attendances->values()->map(function ($attendance, $index) {
            return [
                'no' => $index + 1,
                'name' => $attendance->user->name ?? $attendance->userByFingerId->name,
                'date' => $attendance->created_at->format('Y-m-d'),
                'scanIn' => $attendance->scanIn,
                'scanOut' => $attendance->scanOut,
                'hoursWorked' => $attendance->hoursWorked ?? 'Tidak ada data',
            ];
        });
    }

    /**
     * Get the total hours row for individual export.
     *
     * @return array
     */
    private function getTotalHoursRow()
    {
        $totalHours = $this->calculateTotalHours();
        return [
            'name' => 'Jumlah Total Jam Kerja',
            'no' => '',
            'date' => '',
            'scanIn' => '',
            'scanOut' => '',
            'hoursWorked' => number_format($totalHours, 2) . ' Jam',
        ];
    }

    /**
     * Calculate total hours worked.
     *
     * @return float
     */
    private function calculateTotalHours()
    {
        return $this->attendances->sum(function ($attendance) {
            return (float) filter_var($attendance->hoursWorked, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Tanggal',
            'Scan Masuk',
            'Scan Pulang',
            'Jumlah Jam Kerja',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $this->applyHeaderStyles($sheet);
        $this->applyDataStyles($sheet);

        if ($this->isIndividualExport) {
            $this->applyTotalRowStyles($sheet);
        }

        $this->autoSizeColumns($sheet);

        return [];
    }


    private function autoSizeColumns(Worksheet $sheet)
    {
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    /**
     * Apply header styles.
     *
     * @param Worksheet $sheet
     */
    private function applyHeaderStyles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }

    /**
     * Apply data styles.
     *
     * @param Worksheet $sheet
     */
    private function applyDataStyles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:F' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }

    /**
     * Apply total row styles.
     *
     * @param Worksheet $sheet
     */
    private function applyTotalRowStyles(Worksheet $sheet)
    {
        $sheet->getStyle('A' . ($sheet->getHighestRow()) . ':F' . ($sheet->getHighestRow()))->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ADD8E6'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->mergeCells('A' . ($sheet->getHighestRow()) . ':E' . ($sheet->getHighestRow()));
    }
}
