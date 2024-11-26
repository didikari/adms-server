<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerLog extends Model
{
    protected $fillable = [
        'data',
        'url',
    ];
}