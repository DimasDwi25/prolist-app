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
        $this->setSearchEnabled();
        $this->setPaginationEnabled();
        $this->setPerPageAccepted([5, 10, 25, 50]);
        $this->setPerPage(10);
        $this->setColumnSelectEnabled();
        $this->setDefaultSort('id', 'desc');
    }

    public function query(): Builder
    {
        return Client::query();
    }

    public function columns(): array
    {
        return [
            // Kolom Action
            ButtonGroupColumn::make('Actions')->buttons(array_filter([
                LinkColumn::make('👁 View')
                    ->title(fn($row) => '👁 View')
                    ->location(fn($row) => route('client.show', $row)),
                LinkColumn::make('✏️ Edit')
                    ->title(fn($row) => '✏️ Edit')
                    ->attributes(fn($row) => [
                        'x-data' => '{}',
                        'x-on:click.prevent' => "\$dispatch('open-client-edit-modal', {$row->id})",
                        'class' => 'text-blue-500 hover:underline cursor-pointer',
                    ])
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

            // Kolom data
            Column::make('ID', 'id')->excludeFromColumnSelect()->hideIf(true),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[250px]'>{$value}</div>")
                ->html(),

            Column::make('Phone', 'phone')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[150px]'>{$value}</div>")
                ->html(),

            Column::make('Address', 'address')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[250px]'>{$value}</div>")
                ->html(),

            Column::make('Client Representative', 'client_representative')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[200px]'>{$value}</div>")
                ->html(),

            Column::make('City', 'city')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[150px]'>{$value}</div>")
                ->html(),

            Column::make('Province', 'province')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[150px]'>{$value}</div>")
                ->html(),

            Column::make('Country', 'country')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[150px]'>{$value}</div>")
                ->html(),

            Column::make('Zip Code', 'zip_code')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[100px]'>{$value}</div>")
                ->html(),

            Column::make('Website', 'web')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[200px]'>{$value}</div>")
                ->html(),

            Column::make('Notes', 'notes')
                ->sortable()
                ->searchable()
                ->format(fn($value) => "<div class='truncate max-w-[250px]'>{$value}</div>")
                ->html(),
        ];
    }
}
