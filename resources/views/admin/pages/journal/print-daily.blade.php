<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Voucher - {{ date('d M Y', strtotime($dateLock)) }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; padding: 28px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 18px; }
        .header h2 { font-size: 18px; margin-bottom: 4px; }
        .header p { font-size: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #bbb; padding: 6px 9px; text-align: left; }
        thead th { background-color: #f0f0f0; font-weight: bold; }
        tfoot th { background-color: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .dr { color: #1a7a1a; font-weight: bold; }
        .cr { color: #c0392b; font-weight: bold; }
        .muted { color: #aaa; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 11px; background: #6c757d; color: #fff; }
        .actions { margin-top: 16px; display: flex; gap: 10px; }
        .btn { padding: 7px 18px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-print { background-color: #4154f1; color: #fff; }
        .btn-close-btn { background-color: #6c757d; color: #fff; }
        @media print { .actions { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>Journal Voucher</h2>
        <p>Date: {{ date('d M Y', strtotime($dateLock)) }}</p>
    </div>

    @if($entries->isEmpty())
        <p style="text-align:center;color:#888;margin-top:30px;">No journal entries found for this date.</p>
    @else
    <table>
        <thead>
            <tr>
                <th class="text-center" width="40">#</th>
                <th class="text-center" width="90">Voucher ID</th>
                <th>Account</th>
                <th>Type</th>
                <th class="text-right">Debit (DR)</th>
                <th class="text-right">Credit (CR)</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $key => $entry)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="text-center">{{ $entry->voucher_id ?? 'N/A' }}</td>
                <td>{{ $entry->vendor_name }}</td>
                <td><span class="badge">{{ $entry->vendor_type_name }}</span></td>
                <td class="text-right dr">
                    @if($entry->debit_credit == 2)
                        Rs {{ number_format($entry->amount, 2) }}
                    @else
                        <span class="muted">-</span>
                    @endif
                </td>
                <td class="text-right cr">
                    @if($entry->debit_credit == 1)
                        Rs {{ number_format($entry->amount, 2) }}
                    @else
                        <span class="muted">-</span>
                    @endif
                </td>
                <td>{{ $entry->description }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total:</th>
                <th class="text-right dr">Rs {{ number_format($totalDebit, 2) }}</th>
                <th class="text-right cr">Rs {{ number_format($totalCredit, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="actions">
        <button class="btn btn-print" onclick="window.print()">&#128424; Print</button>
        <button class="btn btn-close-btn" onclick="window.close()">Close</button>
    </div>
</body>
</html>
