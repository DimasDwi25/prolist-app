<?php

namespace App\Livewire\ProjectController;

use App\Models\WorkOrder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class WorkOrderTable extends DataTableComponent
{
    protected $model = WorkOrder::class;

    public bool $searchEnabled = true;
    public bool $paginationEnabled = true;
    public bool $columnSelect = true;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setTableAttributes([
            'class' => 'min-w-full divide-y divide-gray-200',
        ]);
        $this->setTableWrapperAttributes([
            'class' => 'bg-white shadow rounded-2xl overflow-hidden',
        ]);

        // Fix: pakai prefix tabel
        $this->setDefaultSort('work_orders.wo_date', 'desc');
    }


    public function columns(): array
    {
        return [
            Column::make('WO Number', 'wo_kode_no')
                ->sortable()
                ->searchable(),

            Column::make('Project')
                ->label(fn($row) => $row->project->project_name ?? '-')
                ->sortable(function ($builder, $direction) {
                    return $builder->orderBy(
                        \App\Models\Project::select('project_name')
                            ->whereColumn('projects.pn_number', 'work_orders.project_id'),
                        $direction
                    );
                }),

            Column::make('Client')
                ->label(fn($row) => $row->project->quotation->client->name ?? '-'),

            Column::make('WO Date', 'wo_date')
                ->sortable()
                ->format(fn($value) => $value ? date('d M Y', strtotime($value)) : '-'),

            Column::make('PIC')
                ->label(function ($row) {
                    $pics = [];
                    foreach (['pic1User', 'pic2User', 'pic3User', 'pic4User', 'pic5User'] as $relation) {
                        if ($row->$relation) {
                            $pics[] = $row->$relation->name;
                        }
                    }
                    return $pics ? implode(', ', $pics) : '-';
                }),

            Column::make('Total Mandays')
                ->sortable(function ($builder, $direction) {
                    return $builder->orderByRaw('(total_mandays_eng + total_mandays_elect) ' . $direction);
                })
                ->label(fn($row) => $row->total_mandays_eng + $row->total_mandays_elect)
        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return WorkOrder::query()
            ->select('work_orders.*') // Explicitly select work_orders columns
            ->with([
                'project.quotation.client',
                'pic1User',
                'pic2User',
                'pic3User',
                'pic4User',
                'pic5User'
            ]);
    }
}