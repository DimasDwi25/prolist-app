<?php

namespace App\Livewire\ProjectController;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ProjectTable extends DataTableComponent
{
    protected $model = Project::class;

    public function configure(): void
    {
        $this->setPrimaryKey('pn_number');
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
        $user = Auth::user();
        $userId = $user->id;

        $query = Project::query()
            ->with(['category', 'quotation'])
            ->select('*', DB::raw("
                CAST(SUBSTRING(project_number, 4, 2) AS INT) AS year_number,
                CAST(SUBSTRING(project_number, 7, LEN(project_number) - 6) AS INT) AS seq_number
            "));

        // 🔥 Filter berdasarkan role
        if ($user->role->name === 'super_admin') {
            // Super admin bisa lihat semua project
            return $query
                ->orderBy('year_number', 'desc')
                ->orderBy('seq_number', 'desc');
        }

        if ($user->role->name === 'engineer') {
            // Engineer hanya lihat project terkait
            $query->where(function ($q) use ($userId) {
                $q->whereHas('manPowerAllocations', function ($sub) use ($userId) {
                    $sub->where('user_id', $userId);
                })
                ->orWhereHas('phc', function ($sub) use ($userId) {
                    $sub->where('pic_engineering_id', $userId)
                        ->orWhere('ho_engineering_id', $userId);
                });
            });
        }

        return $query
            ->orderBy('year_number', 'desc')
            ->orderBy('seq_number', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('PN Number', 'pn_number')
                ->excludeFromColumnSelect()
                ->hideIf(true),
            Column::make('Project Number', 'project_number')->sortable()->searchable(),
            Column::make('Name', 'project_name')->sortable()->searchable(),
            Column::make('Category', 'category.name')->sortable()->searchable(),
            Column::make('Quotation', 'quotation.no_quotation')->sortable()->searchable(),
            Column::make('Actions')->label(
                fn($row) => Blade::render('components.actions.project-controller.project-actions', ['project' => $row])
            )->html(),
        ];
    }

    public function filters(): array
    {
        $years = Project::query()
            ->selectRaw("
                DISTINCT 
                CASE 
                    WHEN project_number LIKE 'PN-__/%' THEN SUBSTRING(project_number, 4, 2)
                    WHEN project_number LIKE 'CO-PN-__/%' THEN SUBSTRING(project_number, 7, 2)
                    ELSE NULL
                END AS year
            ")
            ->whereNotNull('project_number')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter() // Hapus nilai null
            ->mapWithKeys(function ($year) {
                return [$year => '20' . $year]; // Format menjadi 2023, 2024, dst
            })
            ->toArray();

        return [
            SelectFilter::make('Tahun')
                ->options(['' => '📅 Select Year'] + $years)
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where(function($query) use ($value) {
                            $query->where('project_number', 'like', 'PN-' . $value . '/%')
                                ->orWhere('project_number', 'like', 'CO-PN-' . $value . '/%');
                        });
                    }
                }),
        ];
    }

}
