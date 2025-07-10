@extends('marketing.layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-6 space-y-8">
        <h2 class="text-2xl font-bold text-center mb-4">📄 Quotation Details</h2>

        <div class="space-y-6">
            {{-- Tabs --}}
            <div x-data="{ tab: 'info' }">
                <div class="flex justify-center gap-4 mb-4">
                    <button @click="tab = 'info'"
                        :class="tab === 'info' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded-md font-medium transition">📑 Informasi Quotation</button>
                    <button @click="tab = 'po'"
                        :class="tab === 'po' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded-md font-medium transition">📦 Informasi PO</button>
                    <button @click="tab = 'user'"
                        :class="tab === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded-md font-medium transition">👤 Informasi User</button>
                </div>

                {{-- Tab: Informasi Quotation --}}
                <div x-show="tab === 'info'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-view.label label="No. Quotation" :value="$quotation->no_quotation" />
                    <x-view.label label="Judul" :value="$quotation->title_quotation" />
                    <x-view.label label="Nama Client" :value="$quotation->client->name" />
                    <x-view.label label="PIC Client" :value="$quotation->client_pic" />
                    <x-view.label label="Tanggal Quotation" :value="$quotation->quotation_date->format('d M Y')" />
                    <x-view.label label="Nilai Quotation" :value="number_format($quotation->quotation_value, 0, ',', '.')" />
                    <x-view.label label="Minggu Quotation" :value="$quotation->quotation_weeks" />
                    <x-view.label label="Tanggal Inquiry" :value="$quotation->inquiry_date?->format('d M Y')" />
                    <x-view.label label="Status" :value="match ($quotation->status) {
            'A' => '✓ Completed',
            'D' => '⏳ Belum ada PO',
            'E' => '❌ Dibatalkan',
            'F' => '⚠️ Kalah Tender',
            'O' => '🕒 On Going',
            default => '-'
        }" />
                </div>

                {{-- Tab: Informasi PO --}}
                <div x-show="tab === 'po'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-view.label label="Tanggal PO" :value="$quotation->po_date?->format('d M Y') ?? '-'" />
                    <x-view.label label="Nomor PO" :value="$quotation->po_number ?? '-'" />
                    <x-view.label label="Nilai PO" :value="$quotation->po_value ? number_format($quotation->po_value, 0, ',', '.') : '-'" />
                    <x-view.label label="Minggu Sales" :value="$quotation->sales_weeks ?? '-'" />
                </div>

                {{-- Tab: Informasi User --}}
                <div x-show="tab === 'user'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-view.label label="Created By" :value="$quotation->user->name" />
                    <x-view.label label="Dibuat Pada" :value="$quotation->created_at->format('d M Y H:i')" />
                    <x-view.label label="Diupdate Pada" :value="$quotation->updated_at->format('d M Y H:i')" />
                </div>
            </div>
        </div>
    </div>
@endsection