<?php

namespace App\Livewire\MarketingDirector;

use App\Models\Project;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class SalesReportTable extends DataTableComponent
{
    protected $model = Project::class; // Changed to Project model since po_date is here
    public $selectedYear = '';
    public $selectedMonth = '';

    public function configure(): void
    {
        $this->setPrimaryKey('pn_number');
        $this->setColumnSelectDisabled();
        $this->setPaginationEnabled();
        $this->setPerPageAccepted([5, 10, 25, 50]);
        $this->setPerPage(10);
        $this->setSearchEnabled();
        $this->setFiltersVisibilityEnabled();
        $this->setFilterLayoutPopover();
        $this->setFilterLayoutSlideDown();
    }

    public function builder(): Builder
    {
        return Project::query()
            ->with(['category', 'quotation']);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'pn_number')
                ->excludeFromColumnSelect()
                ->hideIf(true),
            Column::make('Project Number', 'project_number')
                ->sortable()
                ->searchable(),
            Column::make('Name', 'project_name')
                ->sortable()
                ->searchable(),
            Column::make('Category', 'category.name')
                ->sortable()
                ->searchable(),
            Column::make('Quotation', 'quotation.no_quotation')
                ->sortable()
                ->searchable(),
            Column::make('PO Date', 'po_date')
                ->format(fn($value) => $value ? Carbon::parse($value)->format('d M Y') : '-'),
            Column::make('PO Value', 'po_value')
                ->format(fn($value) => $value ? 'Rp '.number_format($value, 0, ',', '.') : '-'),
            Column::make('PO Week', 'sales_weeks'),
            Column::make('PO Number', 'po_number')
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Year')
                ->options($this->getYearOptions())
                ->filter(function (Builder $query, string $value) {
                    $this->selectedYear = $value;
                    if (!empty($value)) {
                        $query->whereYear('po_date', $value);
                    }
                }),

            SelectFilter::make('Month')
                ->options($this->getMonthOptions())
                ->filter(function (Builder $query, string $value) {
                    $this->selectedMonth = $value;
                    if (!empty($value)) {
                        $query->whereMonth('po_date', $value);
                    }
                }),

            SelectFilter::make('PO Date Range')
                ->options([
                    'all' => '📂 All Dates',
                    'weekly' => '🗓️ This Week',
                    'monthly' => '🗓️ This Month',
                    'yearly' => '📅 This Year',
                ])
                ->filter(function (Builder $query, string $value) {
                    $now = now();
                    if ($value === 'weekly') {
                        $query->whereBetween('po_date', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                    } elseif ($value === 'monthly') {
                        $query->whereMonth('po_date', $now->month)
                            ->whereYear('po_date', $now->year);
                    } elseif ($value === 'yearly') {
                        $query->whereYear('po_date', $now->year);
                    }
                }),

            DateFilter::make('From')
                ->filter(function (Builder $query, $value) {
                    if ($value) {
                        $query->whereDate('po_date', '>=', Carbon::parse($value)->startOfDay());
                    }
                }),

            DateFilter::make('To')
                ->filter(function (Builder $query, $value) {
                    if ($value) {
                        $query->whereDate('po_date', '<=', Carbon::parse($value)->endOfDay());
                    }
                }),
        ];
    }

    private function getYearOptions(): array
    {
        return Cache::remember('po-years', now()->addDay(), function () {
            $years = Project::query()
                ->selectRaw('YEAR(po_date) as year')
                ->whereNotNull('po_date')
                ->groupByRaw('YEAR(po_date)')
                ->orderByRaw('YEAR(po_date) DESC')
                ->pluck('year')
                ->toArray();

            $options = ['' => '📅 Select Year'];
            foreach ($years as $year) {
                if ($year) {
                    $options[$year] = $year;
                }
            }

            return $options;
        });
    }

    private function getMonthOptions(): array
    {
        return [
            '' => '🗓️ Select Month',
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
    }

    public function updatedSelectedYear($value): void
    {
        $this->selectedMonth = '';
        $this->reset('selectedMonth');
    }
}
