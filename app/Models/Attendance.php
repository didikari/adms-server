<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'sn',
        'table',
        'stamp',
        'employee_id',
        'timestamp',
        'status1',
        'status2',
        'status3',
        'status4',
        'status5',
        'created_at',
        'updated_at'
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function userByFingerId()
    {
        return $this->belongsTo(User::class, 'employee_id', 'finger_id');
    }
}
