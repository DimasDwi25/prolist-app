<?php

namespace App\Livewire\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Support\Str;

class LogTable extends DataTableComponent
{
    protected $model = Log::class;
    public ?int $projectId = null;

    // Simpan ID row yang sedang di-expand
    public ?int $expandedRow = null;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setTableAttributes([
                'class' => 'min-w-full divide-y divide-gray-200 text-sm text-gray-700',
            ]);
    }

    public function builder(): Builder
    {
        return Log::query()
            ->with(['user', 'responseUser', 'category'])
            ->when($this->projectId, fn($q) => $q->where('project_id', $this->projectId));
    }

    public function columns(): array
    {
        return [
            Column::make('Tanggal', 'tgl_logs')
                ->format(fn($value) => $value ? $value->format('d M Y') : '-')
                ->sortable(),

            Column::make('Kategori', 'category.name')->sortable()->searchable(),

            Column::make('Isi Log', 'logs')
                ->format(function ($value, $row) {
                    $short = e(Str::limit($value, 80));
                    $hasMore = Str::length($value) > 80;
                    $expanded = $this->expandedRow === $row->id;

                    $content = $hasMore
                        ? ($expanded
                            ? e($value) . " <button class='ml-2 text-blue-600 hover:underline text-xs' wire:click='toggleRow({{$row->id}})'>Tutup</button>"
                            : "$short <button class='ml-2 text-blue-600 hover:underline text-xs' wire:click='toggleRow({{$row->id}})'>Baca Selengkapnya</button>")
                        : $short;

                    return "<div class='whitespace-normal break-words max-w-xs'>{$content}</div>";
                })
                ->html()
                ->sortable()
                ->searchable(),


            Column::make('Created By', 'user.name')->sortable(),
            Column::make('Response By', 'responseUser.name')->sortable(),

            Column::make('Status', 'status')
                ->format(fn($value) => "<span class='px-2 py-1 rounded text-xs font-semibold " .
                    ($value === 'open' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') .
                    "'>" . ucfirst($value) . "</span>")
                ->html()
                ->sortable(),
        ];
    }

    // Method untuk expand/collapse row
    public function toggleRow($rowId): void
    {
        $rowId = (int) $rowId; // paksa jadi integer
        $this->expandedRow = $this->expandedRow === $rowId ? null : $rowId;
    }


}
