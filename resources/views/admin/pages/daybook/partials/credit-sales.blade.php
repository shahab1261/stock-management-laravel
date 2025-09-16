<div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
    <h5 class="mb-0"><i class="bi bi-credit-card me-2 text-primary"></i>Credit Sales</h5>
    {{-- <button class="btn btn-sm btn-outline-primary print-section" data-section="credit-sales-section">
        <i class="bi bi-printer me-1"></i>Print
    </button> --}}
</div>

<div id="credit-sales-section" class="table-responsive">
    <table class="table table-hover table-bordered daybook-table" style="width:100%">
        <thead class="table-light">
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Date</th>
                <th class="text-center">Vendor <small style="font-size: 10px">(Debit)</small></th>
                <th class="text-center">Product</th>
                <th class="text-center">Tank Lorry</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Rate</th>
                <th class="text-center">Amount</th>
                <th class="text-center">Description</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp

            @foreach($creditSales as $transaction)
                @php
                    $totalAmount += $transaction->amount;
                    $vendor = app('App\Http\Controllers\DaybookController')->getVendorByType($transaction->vendor_type, $transaction->vendor_id);
                    $product = \App\Models\Management\Product::find($transaction->product_id);
                    $tankLorry = \App\Models\Management\TankLari::find($transaction->vehicle_id);
                @endphp

                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ date('d-m-Y', strtotime($transaction->transasction_date)) }}</td>
                    <td>
                        {{ $vendor->vendor_name }}
                        <span class="badge bg-secondary">{{ $vendor->vendor_type }}</span>
                    </td>
                    <td>{{ $product ? $product->name : 'Not found' }}</td>
                    <td>
                        {{ $tankLorry ? $tankLorry->larry_name : 'No tank lorry found' }}
                    </td>
                    <td>{{ number_format($transaction->quantity, 0, '', ',') }} <small>ltr</small></td>
                    <td>Rs {{ number_format($transaction->rate, 2) }}</td>
                    <td>Rs {{ number_format($transaction->amount, 0, '', ',') }}</td>
                    <td>{{ $transaction->notes }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr class="fw-bold">
                <th colspan="7" class="text-end">Total:</th>
                <th class="text-center">Rs {{ number_format($totalAmount, 0, '', ',') }}</th>
                <th class="text-center">-</th>
            </tr>
        </tfoot>
    </table>
</div>
