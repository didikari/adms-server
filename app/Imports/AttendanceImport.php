<?php

namespace App\Imports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToModel;

class AttendanceImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Attendance([
            'sn'          => $row['sn'],
            'table'       => $row['table'],
            'stamp'       => $row['stamp'],
            'employee_id' => $row['employee_id'],
            'timestamp'   => $row['timestamp'],
            'status1'     => $row['status1'],
            'status2'     => $row['status2'],
            'status3'     => $row['status3'],
            'status4'     => $row['status4'],
            'status5'     => $row['status5'],
        ]);
    }
}
