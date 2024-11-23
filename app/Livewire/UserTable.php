<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Livewire\Attributes\Layout;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

#[Layout("layouts.app")]
class UserTable extends DataTableComponent
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
            Column::make("Id", "id")
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
                ->label(
                    fn($row, Column $column) => view('livewire.datatables.action-column')->with(
                        [
                            'viewLink' => route('users', $row),
                            'editLink' => route('users', $row),
                            'deleteLink' => route('users', $row),
                        ]
                    )
                )->html(),
        ];
    }

    public function exportSelected()
    {
        // Ambil data yang dipilih
        $selected = $this->getSelected();

        // Pastikan ada data yang dipilih
        if (empty($selected)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No rows selected!']);
            return;
        }

        // Lakukan aksi ekspor (misalnya CSV atau Excel)
        return response()->streamDownload(function () use ($selected) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email']); // Header kolom

            foreach (User::whereIn('id', $selected)->get() as $user) {
                fputcsv($handle, [$user->id, $user->name, $user->email]);
            }

            fclose($handle);
        }, 'users-export.csv');
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
