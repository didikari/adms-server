<?php

namespace App\Livewire;

use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReport extends Component
{
    use WithPagination;

    public $title = "My Attendance";
    public $totalWorkedHours;
    public $users;
    public $selectedUser;
    public $startDate;
    public $endDate;
    public $attendances;

    public function mount()
    {
        $this->initializeProperties();
        view()->share('title', $this->title);
    }

    public function filter()
    {
        $this->resetPage();
        $this->resetTotalWorkedHours();
        $this->validateFilters();

        if ($this->isInvalidDateRange()) {
            return;
        }

        try {
            $this->fetchAttendances();
            $this->processAttendances();
        } catch (\Exception $e) {
            $this->dispatchNoDataError();
        }
    }

    private function initializeProperties()
    {
        $this->users = User::all();
        $this->attendances = collect();
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    private function validateFilters()
    {
        $this->validate([
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
        ]);
    }

    private function isInvalidDateRange()
    {
        if ($this->startDate > $this->endDate) {
            $this->dispatchErrorMessage('Tanggal mulai tidak boleh lebih besar dari tanggal akhir.');
            return true;
        }
        return false;
    }

    private function fetchAttendances()
    {
        $startDate = $this->getFormattedDate($this->startDate);
        $endDate = $this->getFormattedDate($this->endDate);

        $this->attendances = $this->getFilteredAttendanceQuery($startDate, $endDate)->get();
        if ($this->attendances->isEmpty()) {
            $this->dispatchNoDataError();
        }
    }

    private function processAttendances()
    {
        $totalWorkedHours = 0;

        $this->groupAttendances($totalWorkedHours);

        if ($this->selectedUser) {
            $this->totalWorkedHours = number_format($totalWorkedHours, 2);
        }
    }

    private function getFormattedDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    private function getFilteredAttendanceQuery($startDate, $endDate)
    {
        return Attendance::query()
            ->when($this->startDate, fn($query) => $query->whereDate('timestamp', '>=', $startDate))
            ->when($this->endDate, fn($query) => $query->whereDate('timestamp', '<=', $endDate))
            ->when($this->selectedUser, fn($query) => $query->where('employee_id', $this->selectedUser))
            ->orderByRaw('YEAR(timestamp), MONTH(timestamp), DAY(timestamp)');
    }

    private function groupAttendances(&$totalWorkedHours)
    {
        $this->attendances = $this->attendances->groupBy(function ($attendance) {
            return $this->getAttendanceGroupKey($attendance);
        });

        $this->attendances = $this->attendances->map(function ($groupedAttendances) use (&$totalWorkedHours) {
            return $this->processAttendanceGroup($groupedAttendances, $totalWorkedHours);
        })->filter();
    }

    private function getAttendanceGroupKey($attendance)
    {
        return $this->selectedUser
            ? $this->getFormattedDate($attendance->timestamp)
            : $attendance->employee_id . '|' . $this->getFormattedDate($attendance->timestamp);
    }

    private function processAttendanceGroup($groupedAttendances, &$totalWorkedHours)
    {
        $entry = $groupedAttendances->where('status1', 0)->sortBy('timestamp')->first();
        $exit = $groupedAttendances->where('status1', 1)->sortByDesc('timestamp')->last();

        if (!$entry || !$exit) {
            return null;
        }

        $duration = $this->calculateWorkedDuration($entry, $exit);
        $this->updateTotalWorkedHours($duration, $totalWorkedHours);

        $attendance = $groupedAttendances->first();
        $attendance->hoursWorked = $duration;
        $attendance->scanIn = Carbon::parse($entry->timestamp)->format('H:i');
        $attendance->scanOut = Carbon::parse($exit->timestamp)->format('H:i');

        return $attendance;
    }

    private function calculateWorkedDuration($entry, $exit)
    {
        $start = Carbon::parse($entry->timestamp)->format('H:i') < '07:00'
            ? Carbon::parse($entry->timestamp)->setTime(7, 0, 0)
            : Carbon::parse($entry->timestamp);

        $end = Carbon::parse($exit->timestamp)->format('H:i') > '17:00'
            ? Carbon::parse($exit->timestamp)->setTime(17, 0, 0)
            : Carbon::parse($exit->timestamp);

        return $this->formatDuration($start->diffInMinutes($end));
    }

    private function formatDuration($durationInMinutes)
    {
        $hours = floor($durationInMinutes / 60);
        $minutes = $durationInMinutes % 60;

        return number_format($hours + ($minutes / 60), 2) . ' Jam';
    }

    private function updateTotalWorkedHours($duration, &$totalWorkedHours)
    {
        if ($this->selectedUser) {
            $totalWorkedHours += (float) filter_var($duration, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        }
    }

    private function resetTotalWorkedHours()
    {
        $this->totalWorkedHours = null;
    }


    private function dispatchErrorMessage($message)
    {
        $this->dispatch('notify', ['type' => 'error', 'message' => $message]);
        $this->attendances = collect();
    }

    private function dispatchNoDataError()
    {
        $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ada data absensi']);
    }

    public function export()
    {
        $this->filter();

        if ($this->attendances->isEmpty()) {
            $this->dispatchErrorMessage('Tidak ada data untuk diekspor');
            return;
        }

        return Excel::download(new AttendanceExport($this->attendances, $this->selectedUser ? true : false), $this->generateFileName());
    }

    private function generateFileName()
    {
        $userName = $this->selectedUser ? User::where('finger_id', $this->selectedUser)->value('name') : 'All_Users';
        return "Attendance_Report_{$userName}_" . Carbon::now()->format('Y-m-d') . ".xlsx";
    }

    public function render()
    {
        return view('livewire.attendance-report', [
            'attendances' => $this->attendances
        ]);
    }
}
