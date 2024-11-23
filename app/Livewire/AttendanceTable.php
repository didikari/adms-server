<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Attendance;
use Livewire\Attributes\Layout;

#[Layout("layouts.app")]
class AttendanceTable extends DataTableComponent
{
    protected $model = Attendance::class;

    public $title = "Attendance";

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc');
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
}
