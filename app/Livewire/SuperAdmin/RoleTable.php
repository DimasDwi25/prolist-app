<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Role;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RoleTable extends DataTableComponent
{
   protected $model = Role::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id'); // 👈 Tambahkan ini untuk menghindari error
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id')->sortable(),
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Type Role', 'type_role')->searchable()->sortable(),
            

            Column::make('Actions')
                ->label(fn($row) => view('livewire.super-admin.role-table', ['role' => $row]))
                ->html(),
        ];
    }
}
