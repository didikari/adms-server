<?php

namespace App\Livewire;

use App\Exports\UsersExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Livewire\Attributes\Layout;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Action;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\WireLinkColumn;

class UserTable extends BaseTableComponent
{
    protected $model = User::class;
    public $title = "Users";
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setBulkActions([
            'exportSelected' => 'Export',
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('No')
                ->label(
                    fn($row, Column $column) => $this->getNumber($row, $column)
                ),
            Column::make("ID", "id")
                ->searchable(),
            Column::make("Name", "name")
                ->searchable(),
            Column::make("Email", "email")
                ->searchable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
            Column::make('Action')
                ->label(function ($row, Column $column) {
                    return view('livewire.datatables.action-column')->with([
                        'editLink' => route('users.edit', $row),
                        'deleteLink' => route('users', $row),
                    ]);
                })->html(),

        ];
    }

    public function exportSelected()
    {
        $selected = $this->getSelected();

        if (empty($selected)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No rows selected!']);
            return;
        }

        return Excel::download(new UsersExport($selected), 'users-export.xlsx');
    }


    public function mount()
    {
        view()->share('title', $this->title);
    }
}
