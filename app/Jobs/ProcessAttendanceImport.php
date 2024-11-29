<?php

namespace App\Jobs;

use App\Events\AttendanceImported;
use App\Imports\AttendanceImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessAttendanceImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filePath = Storage::disk('public')->path($this->filePath);

        // Proses import Excel
        Excel::import(new AttendanceImport, $filePath);

        $message = "Proses import absensi selesai";
        event(new AttendanceImported($message));
    }
}
