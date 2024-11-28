<?php

namespace App\Livewire;

use App\Jobs\ProcessAttendanceImport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Rappasoft\LaravelLivewireTables\Views\Action;

class AttendanceTable extends DataTableComponent
{
    use WithFileUploads;
    protected $model = Attendance::class;

    public $title = "Attendance";
    public $file;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc');
        $this->setActionsInToolbarEnabled();
        $this->setActionsRight();
    }

    public function columns(): array
    {
        return [
            Column::make('No')
                ->label(
                    fn($row, Column $column) => $this->getNumber($row, $column)
                ),
            Column::make('Nama', 'user.name'),
            Column::make("Id", "id")
                ->searchable(),
            Column::make("Sn", "sn")
                ->searchable(),
            Column::make("Table", "table")
                ->searchable(),
            Column::make("Stamp", "stamp")
                ->searchable(),
            Column::make("Employee id", "employee_id")
                ->searchable(),
            Column::make("Timestamp", "timestamp")
                ->searchable(),
            Column::make("Status1", "status1")
                ->searchable(),
            Column::make("Status2", "status2")
                ->searchable(),
            Column::make("Status3", "status3")
                ->searchable(),
            Column::make("Status4", "status4")
                ->searchable(),
            Column::make("Status5", "status5")
                ->searchable(),
            Column::make("Created at", "created_at")
                ->searchable(),
            Column::make("Updated at", "updated_at")
                ->searchable(),
            Column::make('Action')
                ->label(
                    fn($row, Column $column) => view('livewire.datatables.action-column')->with(
                        [
                            'viewLink' => route('attendance', $row),
                            'editLink' => route('attendance', $row),
                            'deleteLink' => route('attendance', $row),
                        ]
                    )
                )->html(),
        ];
    }

    public function mount()
    {
        view()->share('title', $this->title);
    }

    public function getNumber($row)
    {
        static $index = 1;

        $page = $this->getPage();
        $perPage = $this->getPerPage();

        $currentIndex = ($page - 1) * $perPage + $index;
        $index++;

        return $currentIndex;
    }

    public function importExcel()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Simpan file ke storage sementara
        $filePath = $this->file->store('imports');

        // Pastikan file berhasil disimpan sebelum melanjutkan
        if (Storage::exists($filePath)) {
            // Dispatch Job untuk proses impor
            ProcessAttendanceImport::dispatch(storage_path('app/' . $filePath));

            // Pemberitahuan sukses setelah impor
            // session()->flash('message', 'File berhasil diimpor!');
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ada data absensi']);


            // Reset file input
            $this->reset('file');
        } else {
            // Jika file gagal disimpan
            session()->flash('error', 'Gagal mengunggah file.');
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
