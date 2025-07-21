<?php

namespace App\Livewire\SupervisorMarketing;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ProjectTable extends DataTableComponent
{
    protected $model = Project::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');
        $this->setColumnSelectDisabled();
        $this->setPaginationEnabled();
        $this->setPerPageAccepted([5, 10, 25, 50]);
        $this->setPerPage(10);
        $this->setSearchEnabled();
        $this->setFiltersVisibilityEnabled(); // tampilkan tombol filter
        $this->setFilterLayoutSlideDown();    // UI transisi filter
    }

    public function builder(): Builder
    {
        $hasFilters = false;

        // Cek apakah ada search
        if ($this->hasSearch() && !empty($this->getSearch())) {
            $hasFilters = true;
        }

        // Cek applied filters
        $appliedFilters = $this->getAppliedFilters();
        foreach ($appliedFilters as $filterValue) {
            if (!empty($filterValue) && $filterValue !== '') {
                $hasFilters = true;
                break;
            }
        }

        // Jika tidak ada filter/search, return query kosong
        if (!$hasFilters) {
            return Project::query()->whereRaw('1=0');
        }

        return Project::query()->with(['category', 'quotation']);
    }

    public function query(): Builder
    {
        return Project::query()->with(['category', 'quotation']);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->excludeFromColumnSelect()->hideIf(true),
            Column::make('Project Number', 'project_number')->sortable()->searchable(),
            Column::make('Name', 'project_name')->sortable()->searchable(),
            Column::make('Category', 'category.name')->sortable()->searchable(),
            Column::make('Quotation', 'quotation.no_quotation')->sortable()->searchable(),
            Column::make('Actions')->label(
                fn($row) => Blade::render('components.actions.project-actions', ['project' => $row])
            )->html(),
        ];
    }

    public function filters(): array
    {
        $years = Project::selectRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(project_number, '/', 1), '-', -1) as year")
            ->distinct()
            ->pluck('year')
            ->sortDesc()
            ->mapWithKeys(fn($year) => [$year => '20' . $year])
            ->toArray();

        return [
            SelectFilter::make('Tahun')
                ->options(['' => '📅 Select Year'] + $years)
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where('project_number', 'like', '%-' . $value . '/%');
                    }
                })
                ->filterPillTitle('Year'),
        ];
    }
}
