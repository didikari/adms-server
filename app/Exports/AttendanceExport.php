<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $attendances;

    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->attendances->map(function ($attendance) {
            return [
                'name' => $attendance->user->name,
                'date' => $attendance->created_at->format('Y-m-d'),
                'scanIn' => $attendance->scanIn,
                'scanOut' => $attendance->scanOut,
                'hoursWorked' => $attendance->hoursWorked ?? 'Tidak ada data',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Tanggal',
            'Scan Masuk',
            'Scan Pulang',
            'Jumlah Jam Kerja',
        ];
    }
}
