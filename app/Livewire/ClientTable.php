<?php

namespace App\Livewire;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class ClientTable extends DataTableComponent
{
    protected $model = Client::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setColumnSelectDisabled();
        $this->setPaginationEnabled();
        $this->setPerPageAccepted([5, 10, 25, 50]);
        $this->setPerPage(10);
        $this->setSearchEnabled();
    }

    public function query(): Builder
    {
        return Client::query();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->excludeFromColumnSelect()->hideIf(true),
            Column::make('Name', 'name')->searchable()->sortable(),
            Column::make('Phone', 'phone')->searchable()->sortable(),
            Column::make('City', 'city')->searchable()->sortable(),
            Column::make('Country', 'country')->sortable(),

            ButtonGroupColumn::make('Actions')->buttons(array_filter([

                LinkColumn::make('👁 View')
                    ->title(fn($row) => '👁 View')
                    ->location(fn($row) => route('client.show', $row)),

                LinkColumn::make('✏️ Edit')
                    ->title(fn($row) => '✏️ Edit')
                    ->location(fn($row) => route('client.edit', $row)),

                Auth::user()?->role?->name === 'super_admin'
                ? LinkColumn::make('🗑 Delete')
                    ->title(fn($row) => '🗑 Delete')
                    ->location(fn($row) => route('client.destroy', $row))
                    ->attributes(fn($row) => [
                        'onclick' => "return confirm('Delete this client?')",
                        'class' => 'text-red-500',
                    ])
                : null,
            ])),
        ];
    }

}