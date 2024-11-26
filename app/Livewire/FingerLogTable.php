<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\FingerLog;
use Livewire\Attributes\Layout;
use Rappasoft\LaravelLivewireTables\Views\Action;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

#[Layout("layouts.app")]
class FingerLogTable extends DataTableComponent
{
    protected $model = FingerLog::class;
    public $title = "Finger Log";

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('id', 'desc');
        $this->setPerPageAccepted([10, 25, 50, 100]);
    }

    public function columns(): array
    {
        return [
            Column::make('No')
                ->label(
                    fn($row, Column $column) => $this->getNumber($row, $column)
                ),
            Column::make("Id", "id")
                ->searchable(),
            Column::make("Data", "data")
                ->sortable(),
            Column::make("Url", "url")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
            Column::make('Action')
                ->label(
                    fn($row, Column $column) => view('livewire.datatables.action-column')->with(
                        [
                            'viewLink' => route('fingerlog', $row),
                            'editLink' => route('fingerlog', $row),
                            'deleteLink' => route('fingerlog', $row),
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
