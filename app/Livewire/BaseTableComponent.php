<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

abstract class BaseTableComponent extends DataTableComponent
{
    protected function searchableColumn(string $label, string $field): Column
    {
        return Column::make($label, $field)
            ->collapseOnMobile()
            ->searchable();
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
