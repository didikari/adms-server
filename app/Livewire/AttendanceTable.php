<?php

namespace App\Livewire;

use App\Jobs\ProcessAttendanceImport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Attendance;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Rappasoft\LaravelLivewireTables\Views\Action;

class AttendanceTable extends BaseTableComponent
{
    use WithFileUploads;
    protected $model = Attendance::class;

    public $title = "Attendance";
    public $file;
    protected $listeners = ['notify' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc')
            ->setActionsInToolbarEnabled()
            ->setPerPageAccepted([10, 25, 50, 100, 200, 500, 1000]);
    }

    public function columns(): array
    {
        return [
            Column::make('No')
                ->label(fn($row, Column $column) => $this->getNumber($row, $column)),
            $this->searchableColumn('Nama', 'userByFingerId.name'),
            $this->searchableColumn('Id', 'id'),
            $this->searchableColumn('Sn', 'sn'),
            $this->searchableColumn('Table', 'table'),
            $this->searchableColumn('Stamp', 'stamp'),
            $this->searchableColumn('Employee id', 'employee_id'),
            $this->searchableColumn('Timestamp', 'timestamp'),
            $this->searchableColumn('Status1', 'status1'),
            $this->searchableColumn('Status2', 'status2'),
            $this->searchableColumn('Status3', 'status3'),
            $this->searchableColumn('Status4', 'status4'),
            $this->searchableColumn('Status5', 'status5'),
            $this->searchableColumn('Created at', 'created_at'),
            $this->searchableColumn('Updated at', 'updated_at'),
            Column::make('Action')
                ->label(fn($row, Column $column) => view('livewire.datatables.action-column')->with([
                    'viewLink' => route('attendance', $row),
                    'editLink' => route('attendance', $row),
                    'deleteLink' => route('attendance', $row),
                ]))
                ->html(),
        ];
    }

    public function mount()
    {
        view()->share('title', $this->title);
        $this->dispatch('refresh');
    }

    public function importExcel()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $filePath = $this->file->store('imports', 'public');

        if (Storage::disk('public')->exists($filePath)) {
            ProcessAttendanceImport::dispatch($filePath);

            $fileUrl = Storage::url($filePath);

            $this->dispatch('notify', [
                'type' => 'progress',
                'message' => "Proses impor data absensi telah dimulai.",
            ]);

            $this->reset('file');
        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Gagal mengunggah file!",
            ]);
        }
    }


    public function actions(): array
    {
        return [
            Action::make('Import Excel')
                ->setView('livewire.buttons.button-import'),
        ];
    }
}
