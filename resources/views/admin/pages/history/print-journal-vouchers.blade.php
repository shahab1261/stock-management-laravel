<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Vouchers History - {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}</title>
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
        .badge-info { background: #0dcaf0; color: #000; }
        .actions { margin-top: 16px; display: flex; gap: 10px; }
        .btn { padding: 7px 18px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .btn-print { background-color: #4154f1; color: #fff; }
        .btn-close-btn { background-color: #6c757d; color: #fff; }
        @media print { .actions { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>Journal Vouchers History</h2>
        <p>{{ date('d M Y', strtotime($startDate)) }} &mdash; {{ date('d M Y', strtotime($endDate)) }}</p>
    </div>

    @if(empty($processedJournals))
        <p style="text-align:center;color:#888;margin-top:30px;">No journal vouchers found for this period.</p>
    @else
    <table>
        <thead>
            <tr>
                <th class="text-center" width="40">#</th>
                <th>Date</th>
                <th>Account</th>
                <th>Entry Type</th>
                <th class="text-right">Debit (DR)</th>
                <th class="text-right">Credit (CR)</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedJournals as $key => $journal)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ date('d-m-Y', strtotime($journal->transaction_date)) }}</td>
                <td>
                    {{ $journal->vendor->vendor_name }}
                    <span class="badge">{{ $journal->vendor->vendor_type }}</span>
                </td>
                <td><span class="badge badge-info">{{ $journal->entry_type }}</span></td>
                <td class="text-right dr">
                    @if($journal->debit_credit == 2)
                        Rs {{ number_format($journal->amount, 0, '', ',') }}
                    @else
                        <span class="muted">-</span>
                    @endif
                </td>
                <td class="text-right cr">
                    @if($journal->debit_credit == 1)
                        Rs {{ number_format($journal->amount, 0, '', ',') }}
                    @else
                        <span class="muted">-</span>
                    @endif
                </td>
                <td>{{ $journal->description }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total:</th>
                <th class="text-right dr">Rs {{ number_format($journalTotals->total_debit, 0, '', ',') }}</th>
                <th class="text-right cr">Rs {{ number_format($journalTotals->total_credit, 0, '', ',') }}</th>
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
