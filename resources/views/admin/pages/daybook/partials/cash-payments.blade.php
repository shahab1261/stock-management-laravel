<div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
    <h5 class="mb-0"><i class="bi bi-cash me-2 text-primary"></i>Cash Payments</h5>
    {{-- <button class="btn btn-sm btn-outline-primary print-section" data-section="cash-payments-section">
        <i class="bi bi-printer me-1"></i>Print
    </button> --}}
</div>

<div id="cash-payments-section" class="table-responsive">
    <table class="table table-hover daybook-table" style="width:100%">
        <thead class="table-light">
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Date</th>
                <th class="text-center">Account <small style="font-size: 10px">(Debit)</small></th>
                <th class="text-center">Cash <small style="font-size: 10px">(Credit)</small></th>
                <th class="text-center">Amount</th>
                <th class="text-center">Description</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp

            @foreach($cashPayments as $transaction)
                @php
                    $totalAmount += $transaction->amount;
                    $vendor = app('App\Http\Controllers\DaybookController')->getVendorByType($transaction->vendor_type, $transaction->vendor_id);
                @endphp

                <tr>
                    <td>{{ $transaction->tid }}</td>
                    <td>{{ date('d-m-Y', strtotime($transaction->transaction_date)) }}</td>
                    <td>
                        {{ $vendor->vendor_name }}
                        <span class="badge bg-secondary">{{ $vendor->vendor_type }}</span>
                    </td>
                    <td>
                        @if($transaction->payment_type == 1)
                            <span class="badge bg-info">Cash</span>
                        @else
                            {{ $transaction->bank_name }}
                        @endif
                    </td>
                    <td>Rs {{ number_format($transaction->amount, 0, '', ',') }}</td>
                    <td>{{ $transaction->description }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr class="fw-bold">
                <th colspan="4" class="text-end">Total:</th>
                <th class="text-danger">Rs {{ number_format($totalAmount, 0, '', ',') }}</th>
                <th class="text-center">-</th>
            </tr>
        </tfoot>
    </table>
</div>
