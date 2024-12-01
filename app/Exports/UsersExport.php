<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $selected;

    public function __construct(array $selected)
    {
        $this->selected = $selected;
    }

    public function collection()
    {
        return User::whereIn('finger_id', $this->selected)
            ->get(['id', 'name', 'email', 'created_at']);
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Email', 'Created At'];
    }
}
