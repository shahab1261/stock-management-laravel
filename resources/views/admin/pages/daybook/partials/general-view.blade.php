<!-- General View Header -->
<div class="general-view-header mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h4 class="mb-0 text-primary">
                <i class="bi bi-grid-3x3-gap me-2"></i>General View - All Daybook Tables
            </h4>
            <p class="text-muted mb-0 mt-1">Complete overview of all transactions and activities</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="printGeneralView(event)">
                    <i class="bi bi-printer me-1"></i>Print All
                </button>
            </div>
        </div>
    </div>
</div>

<!-- General View Grid Layout -->
<div class="general-view-container">
    <!-- Row 1: Purchase Details & Sales Details -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-cart-plus me-2 text-primary"></i>Purchase Details</h5>
                        <span class="badge bg-primary text-white">{{ count($processedPurchases) }} Records</span>
                    </div>
                    <div class="table-responsive">
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
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-cart-check me-2 text-primary"></i>Sales Details</h5>
                        <span class="badge bg-success text-white">{{ count($processedSales) }} Records</span>
                    </div>
                    <div class="table-responsive">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Credit Sales & Cash Receipts -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-credit-card me-2 text-primary"></i>Credit Sales</h5>
                        <span class="badge bg-warning text-dark">{{ count($creditSales) }} Records</span>
                    </div>
                    <div class="table-responsive">
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
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-cash-coin me-2 text-primary"></i>Cash Receipts</h5>
                        <span class="badge bg-info text-white">{{ count($cashReceipts) }} Records</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered daybook-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Cash <small style="font-size: 10px">(Debit)</small></th>
                                    <th class="text-center">Account <small style="font-size: 10px">(Credit)</small></th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAmount = 0;
                                @endphp

                                @foreach($cashReceipts as $transaction)
                                    @php
                                        $totalAmount += $transaction->amount;
                                        $vendor = app('App\Http\Controllers\DaybookController')->getVendorByType($transaction->vendor_type, $transaction->vendor_id);
                                    @endphp

                                    <tr>
                                        <td>{{ $transaction->tid }}</td>
                                        <td>{{ date('d-m-Y', strtotime($transaction->transaction_date)) }}</td>
                                        <td>
                                            @if($transaction->payment_type == 1)
                                                <span class="badge bg-info">Cash</span>
                                            @else
                                                {{ $transaction->bank_name }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $vendor->vendor_name }}
                                            <span class="badge bg-secondary">{{ $vendor->vendor_type }}</span>
                                        </td>
                                        <td>Rs {{ number_format($transaction->amount, 0, '', ',') }}</td>
                                        <td>{{ $transaction->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-success">Rs {{ number_format($totalAmount, 0, '', ',') }}</th>
                                    <th class="text-center">-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Cash Payments & Bank Receiving -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-cash me-2 text-primary"></i>Cash Payments</h5>
                        <span class="badge bg-danger text-white">{{ count($cashPayments) }} Records</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered daybook-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Cash <small style="font-size: 10px">(Credit)</small></th>
                                    <th class="text-center">Account <small style="font-size: 10px">(Debit)</small></th>
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
                                            @if($transaction->payment_type == 1)
                                                <span class="badge bg-info">Cash</span>
                                            @else
                                                {{ $transaction->bank_name }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $vendor->vendor_name }}
                                            <span class="badge bg-secondary">{{ $vendor->vendor_type }}</span>
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
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-bank me-2 text-primary"></i>Bank Receiving</h5>
                        <span class="badge bg-secondary text-white">{{ count($bankReceiving) }} Records</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered daybook-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Bank <small style="font-size: 10px">(Debit)</small></th>
                                    <th class="text-center">Account <small style="font-size: 10px">(Credit)</small></th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAmount = 0;
                                @endphp

                                @foreach($bankReceiving as $transaction)
                                    @php
                                        $totalAmount += $transaction->amount;
                                        $vendor = app('App\Http\Controllers\DaybookController')->getVendorByType($transaction->vendor_type, $transaction->vendor_id);
                                    @endphp

                                    <tr>
                                        <td>{{ $transaction->tid }}</td>
                                        <td>{{ date('d-m-Y', strtotime($transaction->transaction_date)) }}</td>
                                        <td>
                                            @if($transaction->payment_type == 2)
                                                <span class="badge bg-info">{{ $transaction->bank_name }}</span>
                                            @else
                                                {{ $transaction->bank_name }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ $vendor->vendor_name }}
                                            <span class="badge bg-secondary">{{ $vendor->vendor_type }}</span>
                                        </td>
                                        <td>Rs {{ number_format($transaction->amount, 0, '', ',') }}</td>
                                        <td>{{ $transaction->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-success">Rs {{ number_format($totalAmount, 0, '', ',') }}</th>
                                    <th class="text-center">-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Bank Payments & Journal Vouchers -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-bank me-2 text-primary"></i>Bank Payments</h5>
                        <span class="badge bg-dark text-white">{{ count($bankPayments) }} Records</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered daybook-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Bank <small style="font-size: 10px">(Credit)</small></th>
                                    <th class="text-center">Account <small style="font-size: 10px">(Debit)</small></th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAmount = 0;
                                @endphp

                                @foreach($bankPayments as $transaction)
                                    @php
                                        $totalAmount += $transaction->amount;
                                        $vendor = app('App\Http\Controllers\DaybookController')->getVendorByType($transaction->vendor_type, $transaction->vendor_id);
                                    @endphp

                                    <tr>
                                        <td>{{ $transaction->tid }}</td>
                                        <td>{{ date('d-m-Y', strtotime($transaction->transaction_date)) }}</td>
                                        <td>
                                            @if($transaction->payment_type == 2)
                                                <span class="badge bg-primary">{{ $transaction->bank_name }}</span>
                                            @else
                                                <span class="badge bg-info">Cash</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $vendor->vendor_name }}
                                            <span class="badge bg-secondary">{{ $vendor->vendor_type }}</span>
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
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-journal-text me-2 text-primary"></i>Journal Vouchers</h5>
                        <span class="badge bg-purple text-white">{{ count($journalEntries) }} Records</span>
                    </div>
                    <div class="table-responsive">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Row 5: Purchase Summary & Sales Summary -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Purchase Summary</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseStock as $stock)
                                    <tr>
                                        <td class="fw-medium">{{ $stock->product_name }}</td>
                                        <td class="text-center">{{ number_format($stock->total_quantity, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                                        <td class="text-center"><span class="fw-medium">Rs {{ number_format($stock->total_amount, 0, '', ',') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-center">{{ number_format($purchaseStockTotals->total_stock, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                                    <td class="text-center text-primary">Rs {{ number_format($purchaseStockTotals->total_amount, 0, '', ',') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Sales Summary</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesSummary as $sale)
                                    <tr>
                                        <td class="fw-medium">{{ $sale->product_name }}</td>
                                        <td class="text-center">{{ number_format($sale->total_quantity, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                                        <td class="text-center"><span class="fw-medium">Rs {{ number_format($sale->total_amount, 0, '', ',') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-center">{{ number_format($salesSummaryTotals->total_quantity, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                                    <td class="text-center text-success">Rs {{ number_format($salesSummaryTotals->total_amount, 0, '', ',') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 6: Wet Stock (Full Width) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm general-view-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
                        <h5 class="mb-0"><i class="bi bi-droplet me-2 text-primary"></i>Wet Stock</h5>
                        <span class="badge bg-info text-white">{{ count($processedWetStock) }} Records</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered daybook-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Tank</th>
                                    <th class="text-center">O/Stock</th>
                                    <th class="text-center">Purchase</th>
                                    <th class="text-center">Sales</th>
                                    <th class="text-center">Book Stock</th>
                                    <th class="text-center">Dip Value (mm)</th>
                                    <th class="text-center">Dip Stock</th>
                                    <th class="text-center">Gain/Loss</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processedWetStock as $wetStock)
                                    <tr>
                                        <td>{{ date('d-m-Y', strtotime($wetStock['date'])) }}</td>
                                        <td>{{ $wetStock['tank_name'] }}</td>
                                        <td>{{ number_format($wetStock['opening_stock'], 0, '', ',') }} <small>ltr</small></td>
                                        <td>{{ number_format($wetStock['purchase'], 0, '', ',') }} <small>ltr</small></td>
                                        <td>{{ number_format($wetStock['sales'], 0, '', ',') }} <small>ltr</small></td>
                                        <td>{{ number_format($wetStock['book_stock'], 0, '', ',') }} <small>ltr</small></td>
                                        <td>{{ $wetStock['dip_value'] }} <small>mm</small></td>
                                        <td>{{ number_format($wetStock['dip_stock'], 0, '', ',') }} <small>ltr</small></td>
                                        <td>
                                            @if($wetStock['gain_loss'] > 0)
                                                <span class="text-success fw-bold">+{{ number_format($wetStock['gain_loss'], 0, '', ',') }}</span>
                                            @elseif($wetStock['gain_loss'] < 0)
                                                <span class="text-danger fw-bold">{{ number_format($wetStock['gain_loss'], 0, '', ',') }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                            <small>ltr</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-center">{{ number_format($wetStockTotals->total_purchase, 0, '', ',') }} <small>ltr</small></th>
                                    <th class="text-center">{{ number_format($wetStockTotals->total_sales, 0, '', ',') }} <small>ltr</small></th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">
                                        @if($wetStockTotals->total_gain_loss > 0)
                                            <span class="text-success">+{{ number_format($wetStockTotals->total_gain_loss, 0, '', ',') }}</span>
                                        @elseif($wetStockTotals->total_gain_loss < 0)
                                            <span class="text-danger">{{ number_format($wetStockTotals->total_gain_loss, 0, '', ',') }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                        <small>ltr</small>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Print-only container (hidden normally) -->
<div id="daybook-general-print" style="display:none;"></div>

<style>
@media print {
    body.daybook-printing * { visibility: hidden !important; }
    #daybook-general-print, #daybook-general-print * { visibility: visible !important; }
    #daybook-general-print { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
    .dataTables_length, .dataTables_info, .dataTables_paginate, .dataTables_filter, .dt-buttons, .dt-left-margin, .dt-right-margin { display: none !important; }
    .col-lg-6 { width: 100% !important; }
    .general-view-card { page-break-inside: avoid; break-inside: avoid; }
    .table-responsive { overflow: visible !important; }
}
</style>

<script>
function printGeneralView(e) {
    if (e && typeof e.preventDefault === 'function') e.preventDefault();
    try {
        const container = document.querySelector('.general-view-container');
        if (!container) {
            console.error('General view container not found');
            return;
        }

        // Expand all DataTables to show all rows before cloning
        var datatableStates = [];
        if (window.$ && $.fn && $.fn.DataTable) {
            $('.daybook-table').each(function () {
                try {
                    if ($.fn.DataTable.isDataTable(this)) {
                        var api = $(this).DataTable();
                        var prevLength = api.page.len();
                        var prevPage = api.page();
                        datatableStates.push({ api: api, length: prevLength, page: prevPage });
                        api.page.len(-1).draw(false);
                    }
                } catch (ex) { /* per-table ignore */ }
            });
        }

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const startDate = startDateInput && startDateInput.value ? startDateInput.value : 'N/A';
        const endDate = endDateInput && endDateInput.value ? endDateInput.value : 'N/A';
        const currentDate = new Date().toLocaleDateString();

        // Create a clone of the section and strip DataTables scroll wrappers to real tables
        var sectionClone = container.cloneNode(true);
        try {
            var wrappers = sectionClone.querySelectorAll('.dataTables_wrapper');
            wrappers.forEach(function(wrapper){
                var headTable = wrapper.querySelector('.dataTables_scrollHeadInner table') || wrapper.querySelector('.dataTables_scrollHead table');
                var headThead = headTable ? headTable.querySelector('thead') : null;
                var bodyTable = wrapper.querySelector('.dataTables_scrollBody table');
                var footTable = wrapper.querySelector('.dataTables_scrollFootInner table') || wrapper.querySelector('.dataTables_scrollFoot table');
                var footTFoot = footTable ? footTable.querySelector('tfoot') : null;
                if (bodyTable) {
                    // Build a clean table using thead + tbody (+ tfoot if available)
                    var newTable = document.createElement('table');
                    newTable.setAttribute('style','width:100%;');
                    newTable.className = (bodyTable.className || '') + ' table table-bordered';

                    if (headThead) newTable.appendChild(headThead.cloneNode(true));
                    var tbody = bodyTable.querySelector('tbody');
                    if (tbody) newTable.appendChild(tbody.cloneNode(true));
                    var tfoot = bodyTable.querySelector('tfoot') || footTFoot;
                    if (tfoot) newTable.appendChild(tfoot.cloneNode(true));

                    wrapper.parentNode.replaceChild(newTable, wrapper);
                }
            });
        } catch(ex) { /* non-fatal */ }

        const contentHTML = sectionClone.outerHTML;

        // Build a full HTML document for the new tab
        var printDoc = `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daybook - General Overview</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; color: #212529; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #333; padding-bottom: 12px; }
        .header h1 { margin: 0; color: #333; font-size: 22px; }
        .header p { margin: 6px 0; color: #666; }
        .date-range { text-align: center; font-weight: bold; margin-bottom: 16px; }
        .general-view-card { margin-bottom: 16px; page-break-inside: avoid; break-inside: avoid; }
        .card { border: 1px solid #dee2e6; border-radius: 6px; }
        .card-body { padding: 12px 12px; }
        .badge { display: inline-block; padding: .35em .65em; font-size: .75em; border-radius: .25rem; }
        .bg-primary{ background-color:#0d6efd; color:#fff; }
        .bg-success{ background-color:#198754; color:#fff; }
        .bg-info{ background-color:#0dcaf0; color:#fff; }
        .bg-warning{ background-color:#ffc107; color:#000; }
        .bg-danger{ background-color:#dc3545; color:#fff; }
        .bg-secondary{ background-color:#6c757d; color:#fff; }
        .bg-dark{ background-color:#212529; color:#fff; }
        .bg-purple{ background-color:#6f42c1; color:#fff; }
        .text-primary{ color:#0d6efd; }
        .text-success{ color:#198754; }
        .text-danger{ color:#dc3545; }
        .text-muted{ color:#6c757d; }
        .fw-bold{ font-weight:700; }
        .fw-medium{ font-weight:600; }
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; table-layout: auto; }
        thead { display: table-header-group; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background-color: #f8f9fa; text-align: center; font-weight: 700; }
        tfoot th, tfoot td { background-color: #f8f9fa; font-weight: 700; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .row { display: block; }
        .col-12, .col-lg-6 { width: 100%; }
        .table-responsive { overflow: visible !important; }
        /* Hide DataTables UI if present */
        .dataTables_length, .dataTables_info, .dataTables_paginate, .dataTables_filter, .dt-buttons, .dt-left-margin, .dt-right-margin { display: none !important; }
        @media print { body { margin: 0; } a[href]:after { content: "" !important; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daybook - General Overview</h1>
        <p>Generated on: ${currentDate}</p>
    </div>
    <div class="date-range">Date Range: ${startDate} to ${endDate}</div>
    ${contentHTML}
    <div style="margin-top:16px; text-align:center; font-size:10px; color:#666; border-top:1px solid #ddd; padding-top:8px;">Printed via System</div>
    <script>window.onload = function(){ try{ window.print(); }catch(e){} };<\/script>
</body>
</html>`;

        var blob = new Blob([printDoc], { type: 'text/html' });
        var url = URL.createObjectURL(blob);
        window.open(url, '_blank');

        // restore tables back to original state
        if (datatableStates && datatableStates.length) {
            datatableStates.forEach(function (st) {
                try {
                    st.api.page.len(st.length).draw(false);
                    if (typeof st.page === 'number') {
                        st.api.page(st.page).draw(false);
                    }
                } catch (ex) { /* ignore */ }
            });
        }
    } catch (err) {
        console.error(err);
        alert('Unable to prepare the print view.');
    }
}

</script>
