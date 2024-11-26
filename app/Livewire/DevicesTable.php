<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Device;
use Livewire\Attributes\Layout;


#[Layout('layouts.app')]
class DevicesTable extends DataTableComponent
{
    public $title = 'Devices';  // Contoh nilai default
    protected $model = Device::class;

    public $deviceId, $name, $sn, $online;

    // Validasi data
    protected $rules = [
        'name' => 'required|string|max:255',
        'sn' => 'required|string|max:255',
        'online' => 'required|boolean',
    ];


    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setLoadingPlaceholderStatus(true);
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
            Column::make("Sn", "sn")
                ->searchable(),
            Column::make("Online", "online")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
            Column::make('Action')
                ->label(
                    fn($row, Column $column) => view('livewire.datatables.action-column')->with(
                        [
                            'viewLink' => route('devices', $row->id),
                            'editLink' => route('devices', $row->id),
                            'deleteLink' => route('devices.destroy', $row->id),
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
