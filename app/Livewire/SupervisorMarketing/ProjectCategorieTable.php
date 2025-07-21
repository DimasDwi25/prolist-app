<?php

namespace App\Livewire\SupervisorMarketing;

use App\Models\CategorieProject;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProjectCategorieTable extends DataTableComponent
{
    protected $model = CategorieProject::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');
        $this->setPaginationEnabled();
        $this->setPerPageAccepted([5, 10, 25, 50]);
        $this->setPerPage(10);
        $this->setSearchEnabled();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->excludeFromColumnSelect()
                ->hideIf(true),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Description', 'description')
                ->format(function ($value, $row) {
                    return \Str::limit($value, 80);
                }),

            Column::make('Actions')
                ->label(fn($row) => view('components.actions.category-actions', ['category' => $row]))
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        return CategorieProject::query();
    }
}
