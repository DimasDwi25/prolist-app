{{-- resources/views/pdf/work_order.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Work Order</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px;
        }
        table.detail {
            width: 100%;
            border-collapse: collapse;
        }
        table.detail th, table.detail td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        .signature {
            margin-top: 40px;
            width: 100%;
        }
        .signature td {
            text-align: center;
            padding-top: 40px;
            border: none;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h2>WORK ORDER FORM</h2>
    </div>

    {{-- Informasi Work Order --}}
    <table class="info-table">
        <tr>
            <td style="width: 20%"><strong>No. WO</strong></td>
            <td>: {{ $workOrder->wo_number ?? '-' }}</td>
            <td style="width: 20%"><strong>Tanggal</strong></td>
            <td>: {{ \Carbon\Carbon::parse($workOrder->wo_date ?? now())->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Project</strong></td>
            <td>: {{ $workOrder->project->project_number ?? '-' }}</td>
            <td><strong>Client</strong></td>
            <td>: {{ $workOrder->client->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Alamat</strong></td>
            <td colspan="3">: {{ $workOrder->client->address ?? '-' }}</td>
        </tr>
    </table>

    {{-- Detail Pekerjaan --}}
    <table class="detail">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Deskripsi Pekerjaan</th>
                <th style="width: 20%">PIC</th>
                <th style="width: 15%">Target</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($workOrder->items as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->pic->name ?? '-' }}</td>
                    <td>{{ $item->target_date ? \Carbon\Carbon::parse($item->target_date)->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">Tidak ada detail pekerjaan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan --}}
    <table class="signature">
        <tr>
            <td>Dibuat oleh,<br><br><br>___________________<br>Marketing</td>
            <td>Disetujui oleh,<br><br><br>___________________<br>Manager</td>
            <td>Diterima oleh,<br><br><br>___________________<br>Client</td>
        </tr>
    </table>
</body>
</html>
