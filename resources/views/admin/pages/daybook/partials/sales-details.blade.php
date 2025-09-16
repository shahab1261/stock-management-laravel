<div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
    <h5 class="mb-0"><i class="bi bi-cart-check me-2 text-primary"></i>Sales Details</h5>
    {{-- <button class="btn btn-sm btn-outline-primary print-section" data-section="sales-details-section">
        <i class="bi bi-printer me-1"></i>Print
    </button> --}}
</div>

<div id="sales-details-section" class="table-responsive">
    <table class="table table-hover table-bordered daybook-table" style="width:100%">
        <thead class="table-light">
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Sales Date</th>
                <th class="text-center">Vendor</th>
                <th class="text-center">Product Name</th>
                <th class="text-center">Tank Lorry</th>
                <th class="text-center">Opening Stock</th>
                <th class="text-center">Sold Stock</th>
                <th class="text-center">Closing Stock</th>
                <th class="text-center">Rate</th>
                <th class="text-center">Amount</th>
                <th class="text-center">Freight</th>
                <th class="text-center">Freight Charges</th>
                <th class="text-center">Tank</th>
                <th class="text-center">Sales Type</th>
                <th class="text-center">Profit/Loss</th>
                <th class="text-center">Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedSales as $key => $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ date('d-m-Y', strtotime($sale->create_date)) }}</td>
                    <td>
                        {{ $sale->vendor->vendor_name }}
                        <span class="badge bg-secondary">{{ $sale->vendor->vendor_type }}</span>
                    </td>
                    <td>
                        {{ $sale->product ? $sale->product->name : 'Not found / deleted' }}
                    </td>
                    <td>
                        {{ $sale->tank_lorry ? $sale->tank_lorry->larry_name : 'No tank lorry found' }}
                    </td>
                    <td>{{ number_format($sale->previous_stock, 0, '', ',') }} <small>ltr</small></td>
                    <td>{{ number_format($sale->quantity, 0, '', ',') }} <small>ltr</small></td>
                    <td>{{ number_format($sale->previous_stock - $sale->quantity, 0, '', ',') }} <small>ltr</small></td>
                    <td>Rs {{ number_format($sale->rate, 2) }}</td>
                    <td>Rs {{ number_format($sale->amount, 0, '', ',') }}</td>
                    <td>
                        @if($sale->freight == 1)
                            <span class="badge bg-success">Freight</span>
                        @else
                            <span class="badge bg-warning">Without Freight</span>
                        @endif
                    </td>
                    <td>Rs {{ number_format($sale->freight_charges, 2) }}</td>
                    <td>
                        {{ $sale->tank ? $sale->tank->tank_name : 'Not found' }}
                    </td>
                    <td>
                        @if($sale->sales_type == 1)
                            <span class="badge bg-success">Goddam</span>
                        @else
                            <span class="badge bg-danger">Direct</span>
                        @endif
                    </td>
                    <td>Rs {{ number_format($sale->profit, 0, '', ',') }}</td>
                    <td>{{ $sale->notes }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr>
                <th colspan="5" class="text-end">Total:</th>
                <th class="text-center">-</th>
                <th class="text-center">{{ number_format($salesTotals->total_quantity, 0, '', ',') }} <small>ltr</small></th>
                <th class="text-center">-</th>
                <th class="text-center">-</th>
                <th class="text-center"><small>Rs</small> {{ number_format($salesTotals->total_amount, 0, '', ',') }}</th>
                <th colspan="6">-</th>
            </tr>
        </tfoot>
    </table>
</div>
