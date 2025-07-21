<?php

namespace App\Livewire\SupervisorMarketing;

use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\CustomFilter;


class MarketingReportTable extends DataTableComponent
{
    protected $model = Quotation::class;
    public $selectedYear = '';
    public $selectedMonth = '';

    public function configure(): void
    {
        $this->setPrimaryKey('id'); // 👈 Tambahkan ini untuk menghindari error
        $this->setColumnSelectDisabled();
        $this->setPaginationEnabled();
        $this->setPerPageAccepted([5, 10, 25, 50]);
        $this->setPerPage(10);
        $this->setSearchEnabled();
        $this->setFiltersVisibilityEnabled();
        $this->setFilterLayoutPopover();
        $this->setFilterLayoutSlideDown();

        // $this->setRefreshKeepAlive();

    }

    public function builder(): Builder
    {
        // Cek apakah ada filter atau search yang aktif
        $hasFilters = false;

        // Cek search
        if ($this->hasSearch() && !empty($this->getSearch())) {
            $hasFilters = true;
        }

        // Cek applied filters
        $appliedFilters = $this->getAppliedFilters();
        foreach ($appliedFilters as $filterName => $filterValue) {
            if (!empty($filterValue) && $filterValue !== '' && $filterValue !== 'all') {
                $hasFilters = true;
                break;
            }
        }

        // Jika tidak ada filter aktif, return query kosong
        if (!$hasFilters) {
            return Quotation::query()->whereRaw('1 = 0')->with('client');
        }

        return Quotation::query()->with('client')->whereIn('status', ['A', 'D']);
    }

    public function query(): Builder
    {
        return Quotation::query()
            ->when($this->selectedYear, function ($query) {
                $query->whereYear('quotation_date', $this->selectedYear);
            })
            ->when($this->selectedMonth, function ($query) {
                $query->whereMonth('quotation_date', $this->selectedMonth);
            })
            ->with('client');
    }


    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->excludeFromColumnSelect()
                ->hideIf(true),
            Column::make('No', 'no_quotation')
                ->sortable()
                ->searchable(),

            Column::make('Client', 'client.name')
                ->sortable()
                ->searchable(),

            Column::make('Title', 'title_quotation')
                ->sortable()
                ->searchable(),

            Column::make('PIC', 'client_pic')
                ->sortable()
                ->searchable(),

            Column::make('Quotation Date', 'quotation_date')
                ->sortable()
                ->format(fn($value) => \Carbon\Carbon::parse($value)->format('d M Y')),

            Column::make('Value', 'quotation_value')
                ->sortable()
                ->format(fn($value) => 'Rp' . number_format($value, 0, ',', '.')),
            Column::make('Status', 'status')
                ->format(function ($value) {
                    $statusMap = [
                        'A' => ['label' => '✓ [A] Completed', 'color' => 'bg-green-100 text-green-800'],
                        'D' => ['label' => '⏳[D]  No PO Yet', 'color' => 'bg-yellow-100 text-yellow-800'],
                        'E' => ['label' => '❌[E] Cancelled', 'color' => 'bg-gray-100 text-gray-800'],
                        'F' => ['label' => '⚠️[F] Lost Bid', 'color' => 'bg-red-100 text-red-800'],
                        'O' => ['label' => '🕒[O] On Going', 'color' => 'bg-blue-100 text-blue-800'],
                    ];

                    $status = $statusMap[$value] ?? ['label' => '❓ Unknown', 'color' => 'bg-gray-200 text-gray-600'];
                    return '<span class="text-xs px-2 py-1 rounded-full font-medium ' . $status['color'] . '">' . $status['label'] . '</span>';
                })
                ->html(),


            ButtonGroupColumn::make('Actions')
                ->buttons([
                    LinkColumn::make('View')
                        ->title(fn($row) => '👁 View')
                        ->location(fn($row) => url('/quotation/show/' . $row->id)),
                ]),
        ];
    }

    public function filters(): array
    {
        return [
            // Year Filter - Dynamic options from existing data
            SelectFilter::make('Year')
                ->options($this->getYearOptions())
                ->filter(function (Builder $query, string $value) {
                    $this->selectedYear = $value;
                    if (!empty($value)) {
                        $query->whereYear('quotation_date', $value);
                    }
                }),

            // Month Filter - Dependent on selected year
            SelectFilter::make('Month')
                ->options($this->getMonthOptions())
                ->filter(function (Builder $query, string $value) {
                    $this->selectedMonth = $value;
                    if (!empty($value)) {
                        $query->whereMonth('quotation_date', $value);
                    }
                })
                ->filterPillTitle('Month'),
            SelectFilter::make('Status')
                ->options([
                    'all' => '📂 All Status', // ✅ Tidak kosong
                    'A' => '[A] ✓ Completed',
                    'D' => '[D] ⏳ No PO Yet',
                ])
                ->filter(function (Builder $query, string $value) {
                    if ($value !== 'all') {
                        $query->where('status', $value);
                    }
                }),

            // Filter Weekly, Monthly, Yearly
            SelectFilter::make('Quotation Date Range')
                ->options([
                    'all' => '📂 All Dates',
                    'weekly' => '🗓️ This Week',
                    'monthly' => '🗓️ This Month',
                    'yearly' => '📅 This Year',
                ])
                ->filter(function (Builder $query, string $value) {
                    $now = now();
                    if ($value === 'weekly') {
                        $query->whereBetween('quotation_date', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                    } elseif ($value === 'monthly') {
                        $query->whereMonth('quotation_date', $now->month)
                            ->whereYear('quotation_date', $now->year);
                    } elseif ($value === 'yearly') {
                        $query->whereYear('quotation_date', $now->year);
                    }
                }),

            // Filter Custom Date
            DateFilter::make('From')
                ->filter(function (Builder $query, $value) {
                    if ($value) {
                        $query->whereDate('quotation_date', '>=', Carbon::parse($value)->startOfDay());
                    }
                }),

            DateFilter::make('To')
                ->filter(function (Builder $query, $value) {
                    if ($value) {
                        $query->whereDate('quotation_date', '<=', Carbon::parse($value)->endOfDay());
                    }
                }),
        ];
    }

    private function getYearOptions(): array
    {
        return Cache::remember('quotation-years', now()->addDay(), function () {
            $years = Quotation::query()
                ->selectRaw('YEAR(quotation_date) as year')
                ->groupBy('year')
                ->orderBy('year', 'desc')
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

    /**
     * Get month options - can be filtered by selected year
     */
    private function getMonthOptions(): array
    {
        $months = [
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

        // If a year is selected, only show months that have data for that year
        if ($this->selectedYear) {
            $validMonths = Quotation::query()
                ->whereYear('quotation_date', $this->selectedYear)
                ->selectRaw('MONTH(quotation_date) as month')
                ->groupBy('month')
                ->pluck('month')
                ->toArray();

            $filteredMonths = ['' => '🗓️ Select Month'];
            foreach ($validMonths as $month) {
                if (isset($months[$month])) {
                    $filteredMonths[$month] = $months[$month];
                }
            }
            return $filteredMonths;
        }

        return $months;
    }

    /**
     * Reset month filter when year changes
     */
    public function updatedSelectedYear($value): void
    {
        $this->selectedMonth = '';
        $this->reset('selectedMonth');
    }
}
