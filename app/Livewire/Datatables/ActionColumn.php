<?php

namespace App\Livewire\Datatables;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.app")]
class ActionColumn extends Component
{
    public function render()
    {
        return view('livewire.datatables.action-column');
    }
}
