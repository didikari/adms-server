<?php

namespace App\Livewire;

use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReport extends Component
{
    use WithPagination;
    public $title = "My Attendance";
    public $totalWorkedHours;

    public $users; // Daftar pengguna untuk dropdown filter
    public $selectedUser; // ID pengguna yang dipilih
    public $startDate; // Tanggal awal filter
    public $endDate; // Tanggal akhir filter
    public $attendances; // Data absensi


    public function mount()
    {
        $this->users = User::all();
        $this->attendances = collect();
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d'); // Default tanggal akhir hari ini
        view()->share('title', $this->title);
    }

    public function filter()
    {
        $this->resetTotalWorkedHours(); // Reset total jam kerja
        $this->validateFilters(); // Validasi filter tanggal

        // Cek jika tanggal mulai lebih besar dari tanggal akhir
        if ($this->isInvalidDateRange()) {
            return; // Stop eksekusi jika tanggal tidak valid
        }

        try {
            // Format tanggal untuk query
            $startDate = $this->getFormattedDate($this->startDate);
            $endDate = $this->getFormattedDate($this->endDate);

            // Ambil data absensi sesuai filter
            $query = $this->getFilteredAttendanceQuery($startDate, $endDate);

            // Ambil data absensi berdasarkan query
            $this->attendances = $query->get();

            // Jika tidak ada data, tampilkan pesan error
            if ($this->attendances->isEmpty()) {
                $this->dispatchNoDataError();
                return;
            }

            $totalWorkedHours = 0;

            // Grupkan data absensi berdasarkan kondisi
            $this->groupAttendances($totalWorkedHours);

            // Update total jam kerja jika individu dipilih
            if ($this->selectedUser) {
                $this->totalWorkedHours = number_format($totalWorkedHours, 2);
            }
        } catch (\Exception $e) {
            $this->dispatchNoDataError();
        }
    }

    /**
     * Reset total jam kerja
     */
    private function resetTotalWorkedHours()
    {
        $this->totalWorkedHours = null;
    }

    /**
     * Validasi input tanggal
     */
    private function validateFilters()
    {
        $this->validate([
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
        ]);
    }

    /**
     * Cek apakah tanggal mulai lebih besar dari tanggal akhir
     */
    private function isInvalidDateRange()
    {
        if ($this->startDate > $this->endDate) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.'
            ]);
            $this->attendances = collect(); // Reset data absensi
            return true;
        }
        return false;
    }

    /**
     * Format tanggal yang diberikan menjadi format Y-m-d
     */
    private function getFormattedDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    /**
     * Ambil query absensi berdasarkan filter tanggal dan selectedUser
     */
    private function getFilteredAttendanceQuery($startDate, $endDate)
    {
        return Attendance::query()
            ->when($this->startDate, fn($query) => $query->whereDate('timestamp', '>=', $startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('timestamp', '<=', $endDate))
            ->when($this->selectedUser, fn($query) => $query->where('employee_id', $this->selectedUser))
            ->orderByRaw('YEAR(timestamp), MONTH(timestamp), DAY(timestamp)');
    }

    /**
     * Menangani error jika tidak ada data absensi yang ditemukan
     */
    private function dispatchNoDataError()
    {
        $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ada data absensi']);
    }

    /**
     * Grupkan data absensi berdasarkan kondisi
     */
    private function groupAttendances(&$totalWorkedHours)
    {
        // Tentukan pengelompokan berdasarkan selectedUser atau employee_id
        if (!$this->selectedUser) {
            $this->attendances = $this->attendances->groupBy(function ($attendance) {
                return $attendance->employee_id . '|' . $this->getFormattedDate($attendance->timestamp); // Grupkan berdasarkan employee_id dan tanggal
            });
        } else {
            $this->attendances = $this->attendances->groupBy(function ($attendance) {
                return $this->getFormattedDate($attendance->timestamp); // Grupkan hanya berdasarkan tanggal
            });
        }

        // Proses penghitungan jam kerja untuk setiap grup absensi
        $this->attendances = $this->attendances->map(function ($groupedAttendances) use (&$totalWorkedHours) {
            return $this->processAttendanceGroup($groupedAttendances, $totalWorkedHours);
        })->filter(); // Hapus data yang null
    }

    /**
     * Proses setiap grup absensi untuk menghitung durasi kerja
     */
    private function processAttendanceGroup($groupedAttendances, &$totalWorkedHours)
    {
        // Ambil entri absensi masuk dan pulang
        $entry = $groupedAttendances->where('status1', 0)->sortBy('timestamp')->first();
        $exit = $groupedAttendances->where('status1', 1)->sortByDesc('timestamp')->last();

        // Jika tidak ada absensi masuk atau pulang, abaikan grup ini
        if (!$entry || !$exit) {
            return null;
        }

        // Hitung durasi kerja
        $duration = $this->calculateWorkedDuration($entry, $exit);
        $this->updateTotalWorkedHours($duration, $totalWorkedHours);

        // Simpan jam kerja pada absensi
        $attendance = $groupedAttendances->first();
        $attendance->hoursWorked = $duration;
        $attendance->scanIn = Carbon::parse($entry->timestamp)->format('H:i');
        $attendance->scanOut = Carbon::parse($exit->timestamp)->format('H:i');

        return $attendance;
    }

    /**
     * Menghitung durasi kerja berdasarkan jam masuk dan pulang
     */
    private function calculateWorkedDuration($entry, $exit)
    {
        $start = Carbon::parse($entry->timestamp);
        if ($start->format('H:i') < '07:00') {
            $start = $start->setTime(7, 0, 0); // Set jam masuk menjadi 07:00 jika lebih awal
        }

        $end = Carbon::parse($exit->timestamp);
        if ($end->format('H:i') > '17:00') {
            $end = $end->setTime(17, 0, 0); // Set jam pulang menjadi 17:00 jika lebih dari itu
        }

        $durationInMinutes = $start->diffInMinutes($end);
        $durationInHours = floor($durationInMinutes / 60);
        $remainingMinutes = $durationInMinutes % 60;

        return number_format($durationInHours + ($remainingMinutes / 60), 2) . ' Jam';
    }

    /**
     * Update total jam kerja jika individu dipilih
     */
    private function updateTotalWorkedHours($duration, &$totalWorkedHours)
    {
        if ($this->selectedUser) {
            $hours = (float) filter_var($duration, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $totalWorkedHours += $hours;
        }
    }

    public function export()
    {
        $this->filter();
        if ($this->attendances->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Tidak ada data untuk diekspor',
            ]);
            return;
        }

        return Excel::download(new AttendanceExport($this->attendances), 'attendance-report.xlsx');
    }


    public function render()
    {
        return view('livewire.attendance-report', [
            'attendances' => $this->attendances,
        ]);
    }
}
