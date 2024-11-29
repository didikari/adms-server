<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\FingerLog;
use Livewire\Attributes\Layout;
use Rappasoft\LaravelLivewireTables\Views\Action;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class FingerLogTable extends BaseTableComponent
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
            $this->searchableColumn('Id', 'id'),
            $this->searchableColumn('Data', 'data'),
            $this->searchableColumn('Url', 'url'),
            $this->searchableColumn('Created at', 'created_at'),
            $this->searchableColumn('Updated at', 'updated_at'),
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
}
