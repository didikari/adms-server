<?php

namespace App\Jobs;

use App\Imports\AttendanceImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
        try {
            // Log message before import to track the process
            Log::info('Starting attendance import', ['file' => $this->filePath]);

            // Import the Excel file using AttendanceImport
            Excel::import(new AttendanceImport, $this->filePath);

            // Log successful import
            Log::info('Attendance import successful', ['file' => $this->filePath]);

            // Optionally delete the file after processing
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
                Log::info('File deleted after import', ['file' => $this->filePath]);
            }
        } catch (\Exception $e) {
            // Log the error with detailed information
            Log::error('Attendance import failed', [
                'error' => $e->getMessage(),
                'file' => $this->filePath,  // Use the correct file variable
                'stack_trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
