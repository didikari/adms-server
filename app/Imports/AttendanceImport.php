<?php

namespace App\Imports;

use App\Models\Attendance;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AttendanceImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            return new Attendance([
                'sn'          => $row['sn'] ?? 'default_sn',
                'table'       => $row['table'] ?? 'default_table',
                'stamp'       => $row['stamp'] ?? 'default_stamp',
                'employee_id' => $row['employee_id'] ?? 0,
                'timestamp'   => $row['timestamp'] ?? now(),
                'status1'     => $row['status1'] ?? null,
                'status2'     => $row['status2'] ?? null,
                'status3'     => $row['status3'] ?? null,
                'status4'     => $row['status4'] ?? null,
                'status5'     => $row['status5'] ?? null,
                'created_at'  => $row['created_at'] ?? now(),
                'updated_at'  => $row['updated_at'] ?? now(),
            ]);
        } catch (Exception $e) {
            Log::error('Error processing row:', ['row' => $row, 'error' => $e->getMessage()]);
        }
    }

    public function chunkSize(): int
    {
        return 5000;
    }
}
