<div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
    <h5 class="mb-0"><i class="bi bi-cart-plus me-2 text-primary"></i>Purchase Details</h5>
    {{-- <button class="btn btn-sm btn-outline-primary print-section" data-section="purchase-details-section">
        <i class="bi bi-printer me-1"></i>Print
    </button> --}}
</div>

<div id="purchase-details-section" class="table-responsive">
    <table class="table table-hover table-bordered daybook-table" style="width:100%">
        <thead class="table-light">
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Purchase Date</th>
                <th class="text-center">Vendor</th>
                <th class="text-center">Product</th>
                <th class="text-center">Tank Lorry</th>
                <th class="text-center">Opening Stock</th>
                <th class="text-center">Purchased Stock</th>
                <th class="text-center">Closing Stock</th>
                <th class="text-center">Rate</th>
                <th class="text-center">Amount</th>
                <th class="text-center">Driver</th>
                <th class="text-center">Stock Sold</th>
                <th class="text-center">Tank</th>
                <th class="text-center">Receipt</th>
                <th class="text-center">Comments</th>
                <th class="text-center">Rate Adjustment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedPurchases as $key => $purchase)
                <tr>
                    <td>{{ $purchase->id }}</td>
                    <td>{{ date('d-m-Y', strtotime($purchase->purchase_date)) }}</td>
                    <td>
                        {{ $purchase->vendor->vendor_name }}
                        <span class="badge bg-secondary">{{ $purchase->vendor->vendor_type }}</span>
                    </td>
                    <td>
                        {{ $purchase->product ? $purchase->product->name : 'Not found / deleted' }}
                    </td>
                    <td>
                        {{ $purchase->tank_lorry ? $purchase->tank_lorry->larry_name : 'Not found' }}
                    </td>
                    <td>{{ number_format($purchase->previous_stock, 0, '', ',') }} <small>ltr</small></td>
                    <td>{{ number_format($purchase->stock, 0, '', ',') }} <small>ltr</small></td>
                    <td>{{ number_format($purchase->previous_stock + $purchase->stock, 0, '', ',') }} <small>ltr</small></td>
                    <td><small>Rs</small> {{ number_format($purchase->rate, 2) }}</td>
                    <td><small>Rs</small> {{ number_format($purchase->total_amount, 0, '', ',') }}</td>
                    <td>
                        {{ $purchase->driver ? $purchase->driver->driver_name : 'Not found' }}
                    </td>
                    <td>{{ number_format($purchase->sold_quantity, 0, '', ',') }}</td>
                    <td>
                        {{ $purchase->tank ? $purchase->tank->tank_name : 'Not found' }}
                    </td>
                    <td>
                        @if($purchase->image_path)
                            <a href="{{ $purchase->image_path }}" target="_blank" class="text-primary">View Receipt</a>
                        @else
                            <span class="text-muted">Not uploaded</span>
                        @endif
                    </td>
                    <td>{{ $purchase->comments }}</td>
                    <td><small>Rs</small> {{ number_format($purchase->rate_adjustment, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr>
                <th colspan="5" class="text-end">Total:</th>
                <th class="text-center">-</th>
                <th class="text-center">{{ number_format($purchaseTotals->total_stock, 0, '', ',') }} <small>ltr</small></th>
                <th class="text-center">-</th>
                <th class="text-center">-</th>
                <th class="text-center"><small>Rs</small> {{ number_format($purchaseTotals->total_amount, 0, '', ',') }}</th>
                <th colspan="6">-</th>
            </tr>
        </tfoot>
    </table>
</div>
