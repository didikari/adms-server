<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Device;
use Livewire\Attributes\Layout;


class DevicesTable extends BaseTableComponent
{
    public $title = 'Devices';
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
                ->label(fn($row, Column $column) => $this->getNumber($row, $column))
                ->collapseOnMobile(),
            $this->searchableColumn('Id', 'id'),
            Column::make('Name', 'name'),
            $this->searchableColumn('Sn', 'sn'),
            $this->searchableColumn('Online', 'online')
                ->sortable(),
            $this->searchableColumn('Created at', 'created_at')
                ->sortable(),
            $this->searchableColumn('Updated at', 'updated_at')
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
}
