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
            Column::make('Status', 'status')
                ->format(function ($value) {
                    $class = match(strtolower(trim($value))) {
                        'open'     => 'bg-yellow-100 text-yellow-800',
                        'close'    => 'bg-gray-100 text-gray-800',
                        'approved' => 'bg-green-100 text-green-800',
                        default    => 'bg-orange-100 text-orange-800'
                    };
                    return "<span class='px-2 py-1 rounded text-xs font-semibold {$class}'>"
                            . ucfirst($value) . "</span>";
                })
                ->html()
                ->sortable(),

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
            Column::make('User ID', 'users_id')->hideIf(true),
            Column::make('ID', 'id')->hideIf(true),
            // Column::make('Actions')
            //     ->label(function ($row) {
            //         if (trim(strtolower($row->status)) !== 'close' && (int)$row->users_id === (int)auth()->id()) {
            //             return <<<HTML
            //                 <button wire:click="closeLog({$row->id})" 
            //                         class="px-2 py-1 bg-red-600 text-white rounded text-xs">
            //                     Close
            //                 </button>
            //             HTML;
            //         }
            //         return '-';
            //     })
            //     ->html(),
            Column::make('Actions')
                ->label(function ($row) {
                    $buttons = '';

                    if ((int)$row->users_id === (int)auth()->id()) {
                        $buttons .= <<<HTML
                            <button wire:click="closeLog({$row->id})" 
                                    class="px-2 py-1 bg-red-600 text-white rounded text-xs mr-1">
                                Close
                            </button>
                        HTML;

                        $buttons .= <<<HTML
                            <button type="button"
                                onclick='Livewire.dispatch("editLog", {"id": {$row->id}})'
                                class="px-2 py-1 bg-blue-600 text-white rounded text-xs">
                                Edit
                            </button>
                        HTML;

                    }

                    return $buttons ?: '-';
                })
                ->html(),


            
        ];
    }

    // Method untuk expand/collapse row
    public function toggleRow($rowId): void
    {
        $rowId = (int) $rowId; // paksa jadi integer
        $this->expandedRow = $this->expandedRow === $rowId ? null : $rowId;
    }

    public function closeLog($id)
    {
        $log = Log::findOrFail($id);

        $authId = (int) auth()->id();
        $ownerId = (int) $log->users_id;

        // Debug sementara (biar tahu siapa lawan siapa)
        if ($ownerId !== $authId) {
            Log::warning("Close log ditolak. Auth ID: {$authId}, Log Owner: {$ownerId}");
            abort(403, "Hanya pembuat log yang bisa menutup log ini. Auth ID: {$authId}, Owner ID: {$ownerId}");
        }

        $log->update([
            'status'        => 'close',
            'closing_date'  => now(),
            'closing_users' => $authId,
        ]);

        event(new \App\Events\LogClosed($log));

        $this->dispatch('log-success', message: 'Log berhasil ditutup.');
    }




}
