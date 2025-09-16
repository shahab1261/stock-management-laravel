<div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
    <h5 class="mb-0"><i class="bi bi-journal-text me-2 text-primary"></i>Journal Vouchers</h5>
    {{-- <button class="btn btn-sm btn-outline-primary print-section" data-section="journal-vouchers-section">
        <i class="bi bi-printer me-1"></i>Print
    </button> --}}
</div>

<div id="journal-vouchers-section" class="table-responsive">
    <table class="table table-hover table-bordered daybook-table" style="width:100%">
        <thead class="table-light">
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Date</th>
                <th class="text-center">Accounts</th>
                <th class="text-center">Debit</th>
                <th class="text-center">Credit</th>
                <th class="text-center">Description</th>
            </tr>
        </thead>
        <tbody>
            @php
                $debitAmount = 0;
                $creditAmount = 0;
            @endphp

            @foreach($journalEntries as $transaction)
                @php
                    $vendor = app('App\Http\Controllers\DaybookController')->getVendorByType($transaction->vendor_type, $transaction->vendor_id);
                @endphp

                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ date('d-m-Y', strtotime($transaction->transaction_date)) }}</td>
                    <td>
                        {{ $vendor->vendor_name }}
                        <span class="badge bg-secondary">{{ $vendor->vendor_type }}</span>
                    </td>
                    <td>
                        @if($transaction->debit_credit == 1)
                            -
                        @else
                            @php $debitAmount += $transaction->amount; @endphp
                            Rs {{ number_format($transaction->amount, 0, '', ',') }}
                        @endif
                    </td>
                    <td>
                        @if($transaction->debit_credit == 1)
                            @php $creditAmount += $transaction->amount; @endphp
                            Rs {{ number_format($transaction->amount, 0, '', ',') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $transaction->description }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr class="fw-bold">
                <th colspan="3" class="text-end">Total:</th>
                <th class="text-danger">Rs {{ number_format($debitAmount, 0, '', ',') }}</th>
                <th class="text-success">Rs {{ number_format($creditAmount, 0, '', ',') }}</th>
                <th class="text-center">-</th>
            </tr>
        </tfoot>
    </table>
</div>
