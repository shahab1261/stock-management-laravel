@extends('admin.layout.master')

@section('title', 'Account History Report')
@section('description', 'Comprehensive account history and transaction details')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/account-history.css') }}">
@endsection

@section('content')

<style>
    .account-history-table tfoot:not(:first-child) {
        display: none;
    }
</style>
@permission('reports.account-history.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Account History Report</h3>
            <p class="text-muted mb-0">
                @if(!empty($vendorName))
                    Account history for <strong>{{ $vendorName }}</strong> from {{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }}
                @else
                    Select an account to view comprehensive transaction history
                @endif
            </p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Account & Date Filters</h5>
                    <!-- Account Balance Widget (if available) -->
                    @if(!empty($vendorName))
                        <div class="account-balance-widget d-flex align-items-center">
                            <div class="account-balance-icon me-3">
                                <i class="bi bi-person-badge text-primary fs-4"></i>
                            </div>
                            <div class="account-balance-content">
                                <small class="text-muted d-block mb-0">Selected Account</small>
                                <span class="account-balance-name text-primary fw-bold">{{ $vendorName }}</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.account-history') }}" method="GET" class="row align-items-end">
                        <!-- Vendor Selection -->
                        <div class="mb-3" style="width: 254px;">
                            <label for="vendor_dropdown" class="form-label">Select Account</label>
                            <select name="vendor_dropdown" id="vendor_dropdown" class="form-select" required>
                                <option value="">Choose Account...</option>

                                <optgroup label="Suppliers">
                                    @foreach(App\Models\Management\Suppliers::orderBy('name')->get() as $supplier)
                                        <option value="{{ $supplier->id }}"
                                                data-type="1"
                                                data-name="{{ $supplier->name }}"
                                                {{ ($vendorId == $supplier->id && $vendorType == '1') ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Customers">
                                    @foreach(App\Models\Management\Customers::orderBy('name')->get() as $customer)
                                        <option value="{{ $customer->id }}"
                                                data-type="2"
                                                data-name="{{ $customer->name }}"
                                                {{ ($vendorId == $customer->id && $vendorType == '2') ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Products">
                                    @foreach(App\Models\Management\Product::orderBy('name')->get() as $product)
                                        <option value="{{ $product->id }}"
                                                data-type="3"
                                                data-name="{{ $product->name }}"
                                                {{ ($vendorId == $product->id && $vendorType == '3') ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Expenses">
                                    @foreach(App\Models\Management\Expenses::orderBy('expense_name')->get() as $expense)
                                        <option value="{{ $expense->eid }}"
                                                data-type="4"
                                                data-name="{{ $expense->expense_name }}"
                                                {{ ($vendorId == $expense->eid && $vendorType == '4') ? 'selected' : '' }}>
                                            {{ $expense->expense_name }}
                                        </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Incomes">
                                    @foreach(App\Models\Management\Incomes::orderBy('income_name')->get() as $income)
                                        <option value="{{ $income->id }}"
                                                data-type="5"
                                                data-name="{{ $income->income_name }}"
                                                {{ ($vendorId == $income->id && $vendorType == '5') ? 'selected' : '' }}>
                                            {{ $income->income_name }}
                                        </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Banks">
                                    @foreach(App\Models\Management\Banks::orderBy('name')->get() as $bank)
                                        <option value="{{ $bank->id }}"
                                                data-type="6"
                                                data-name="{{ $bank->name }}"
                                                {{ ($vendorId == $bank->id && $vendorType == '6') ? 'selected' : '' }}>
                                            {{ $bank->name }}
                                        </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Cash">
                                    <option value="7"
                                            data-type="7"
                                            data-name="Cash"
                                            {{ ($vendorType == '7') ? 'selected' : '' }}>
                                        Cash
                                    </option>
                                </optgroup>

                                <optgroup label="MP">
                                    <option value="8"
                                            data-type="8"
                                            data-name="MP"
                                            {{ ($vendorType == '8') ? 'selected' : '' }}>
                                        MP
                                    </option>
                                </optgroup>

                                <optgroup label="Employees">
                                    @foreach(App\Models\User::where('user_type', 3)->orderBy('name')->get() as $employee)
                                        <option value="{{ $employee->id }}"
                                                data-type="9"
                                                data-name="{{ $employee->name }}"
                                                {{ ($vendorId == $employee->id && $vendorType == '9') ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="mb-3" style="width: 204px;">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>

                        <div class="mb-3" style="width: 204px;">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-search me-2"></i>Filter
                            </button>
                        </div>

                        <div class="col-md-2 mb-2">
                            <a href="{{ route('admin.reports.account-history') }}" class="btn btn-secondary w-100 mb-2">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                            </a>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $vendorId }}">
                        <input type="hidden" name="vendor_type" id="vendor_type" value="{{ $vendorType }}">
                        <input type="hidden" name="vendor_name" id="vendor_name" value="{{ $vendorName }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Content -->
    @if(!empty($vendorId) && !empty($vendorType))
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white p-0">
                        <ul class="nav nav-tabs border-bottom-0" id="accountHistoryTabs" role="tablist">
                            <li class="nav-item" style="padding: 6px;" role="presentation">
                                <button class="nav-link active" id="purchase-tab" data-bs-toggle="tab" data-bs-target="#purchase" type="button" role="tab">
                                    <i class="bi bi-cart-plus me-2"></i>Purchase History
                                </button>
                            </li>
                            <li class="nav-item" style="padding: 6px;" role="presentation">
                                <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                                    <i class="bi bi-cart-check me-2"></i>Sales History
                                </button>
                            </li>
                            <li class="nav-item" style="padding: 6px;" role="presentation">
                                <button class="nav-link" id="credit-sales-tab" data-bs-toggle="tab" data-bs-target="#credit-sales" type="button" role="tab">
                                    <i class="bi bi-credit-card me-2"></i>Credit Sales
                                </button>
                            </li>
                            <li class="nav-item" style="padding: 6px;" role="presentation">
                                <button class="nav-link" id="cash-transactions-tab" data-bs-toggle="tab" data-bs-target="#cash-transactions" type="button" role="tab">
                                    <i class="bi bi-cash-coin me-2"></i>Cash Transactions
                                </button>
                            </li>
                            <li class="nav-item" style="padding: 6px;" role="presentation">
                                <button class="nav-link" id="bank-transactions-tab" data-bs-toggle="tab" data-bs-target="#bank-transactions" type="button" role="tab">
                                    <i class="bi bi-bank me-2"></i>Bank Transactions
                                </button>
                            </li>
                            <li class="nav-item" style="padding: 6px;" role="presentation">
                                <button class="nav-link" id="journal-tab" data-bs-toggle="tab" data-bs-target="#journal" type="button" role="tab">
                                    <i class="bi bi-journal-text me-2"></i>Journal Vouchers
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content" id="accountHistoryTabsContent">
                            <!-- Purchase Tab -->
                            <div class="tab-pane fade show active" id="purchase" role="tabpanel">
                                <div class="p-4">
                                    <!-- Purchase Details Card -->
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-cart-plus text-primary me-2"></i>Purchase Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Product</th>
                                                            <th class="text-center">Quantity</th>
                                                            <th class="text-center">Rate</th>
                                                            <th class="text-center">Amount</th>
                                                            <th class="text-center">Vehicle</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($purchases) && count($purchases) > 0)
                                                            @php
                                                                $totalStock = 0;
                                                                $totalAmount = 0;
                                                            @endphp
                                                            @foreach($purchases as $purchase)
                                                                @php
                                                                    $totalStock += $purchase->stock;
                                                                    $totalAmount += $purchase->total_amount;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-center">{{ $purchase->id }}</td>
                                                                    <td class="text-center">{{ date('d-m-Y', strtotime($purchase->purchase_date)) }}</td>
                                                                    <td class="text-center">{{ $purchase->product->name ?? 'Unknown' }}</td>
                                                                    <td class="text-center">{{ number_format($purchase->stock) }} <small class="text-muted">ltr</small></td>
                                                                    <td class="text-center">Rs {{ number_format($purchase->rate, 2) }}</td>
                                                                    <td class="text-center">Rs {{ number_format($purchase->total_amount) }}</td>
                                                                    <td class="text-center">{{ $purchase->vehicle_no ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    @if(isset($purchases) && count($purchases) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td colspan="3" class="text-center">Total</td>
                                                                <td class="text-center">{{ number_format($totalStock) }} <small class="text-muted">ltr</small></td>
                                                                <td class="text-center">-</td>
                                                                <td class="text-center">Rs {{ number_format($totalAmount) }}</td>
                                                                <td class="text-center">-</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Purchase Summary Card -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-bar-chart text-success me-2"></i>Purchase Summary</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Product Name</th>
                                                            <th class="text-center">Quantity</th>
                                                            <th class="text-center">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($purchaseStock) && count($purchaseStock) > 0)
                                                            @php
                                                                $totalStock = 0;
                                                                $totalAmount = 0;
                                                            @endphp
                                                            @foreach($purchaseStock as $stock)
                                                                @php
                                                                    $totalStock += $stock->total_quantity;
                                                                    $totalAmount += $stock->total_amount;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $stock->product_name }}</td>
                                                                    <td class="text-center">{{ number_format($stock->total_quantity) }}</td>
                                                                    <td class="text-center">Rs {{ number_format($stock->total_amount) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    @if(isset($purchaseStock) && count($purchaseStock) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td>Total</td>
                                                                <td class="text-center">{{ number_format($totalStock) }}</td>
                                                                <td class="text-center">Rs {{ number_format($totalAmount) }}</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sales Tab -->
                            <div class="tab-pane fade" id="sales" role="tabpanel">
                                <div class="p-4">
                                    <!-- Sales Details Card -->
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-cart-check text-info me-2"></i>Sales Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Product</th>
                                                            <th class="text-center">Quantity</th>
                                                            <th class="text-center">Rate</th>
                                                            <th class="text-center">Amount</th>
                                                            <th class="text-center">Vehicle</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($sales) && count($sales) > 0)
                                                            @php
                                                                $totalQuantity = 0;
                                                                $totalAmount = 0;
                                                            @endphp
                                                            @foreach($sales as $sale)
                                                                @php
                                                                    $totalQuantity += $sale->quantity;
                                                                    $totalAmount += $sale->amount;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-center">{{ $sale->id }}</td>
                                                                    <td class="text-center">{{ date('d-m-Y', strtotime($sale->create_date)) }}</td>
                                                                    <td class="text-center">{{ $sale->product->name ?? 'Unknown' }}</td>
                                                                    <td class="text-center">{{ number_format($sale->quantity) }} <small class="text-muted">ltr</small></td>
                                                                    <td class="text-center">Rs {{ number_format($sale->rate, 2) }}</td>
                                                                    <td class="text-center">Rs {{ number_format($sale->amount) }}</td>
                                                                    <td class="text-center">{{ $sale->tank_lari_id ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach

                                                        @endif
                                                    </tbody>
                                                    @if(isset($sales) && count($sales) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td colspan="3" class="text-center">Total</td>
                                                                <td class="text-center">{{ number_format($totalQuantity) }} <small class="text-muted">ltr</small></td>
                                                                <td class="text-center">-</td>
                                                                <td class="text-center">Rs {{ number_format($totalAmount) }}</td>
                                                                <td class="text-center">-</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sales Summary Card -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-pie-chart text-warning me-2"></i>Sales Summary</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Product Name</th>
                                                            <th class="text-center">Quantity</th>
                                                            <th class="text-center">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($salesSummary) && count($salesSummary) > 0)
                                                            @php
                                                                $totalQuantity = 0;
                                                                $totalAmount = 0;
                                                            @endphp
                                                            @foreach($salesSummary as $summary)
                                                                @php
                                                                    $totalQuantity += $summary->total_quantity;
                                                                    $totalAmount += $summary->total_amount;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $summary->product_name }}</td>
                                                                    <td class="text-center">{{ number_format($summary->total_quantity) }}</td>
                                                                    <td class="text-center">Rs {{ number_format($summary->total_amount) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    @if(isset($salesSummary) && count($salesSummary) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td>Total</td>
                                                                <td class="text-center">{{ number_format($totalQuantity) }}</td>
                                                                <td class="text-center">Rs {{ number_format($totalAmount) }}</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Credit Sales Tab -->
                            <div class="tab-pane fade" id="credit-sales" role="tabpanel">
                                <div class="p-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-credit-card text-danger me-2"></i>Credit Sales</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Product</th>
                                                            <th class="text-center">Quantity</th>
                                                            <th class="text-center">Rate</th>
                                                            <th class="text-center">Amount</th>
                                                            <th class="text-center">Vehicle</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($creditSales) && count($creditSales) > 0)
                                                            @php
                                                                $totalAmount = 0;
                                                            @endphp
                                                            @foreach($creditSales as $creditSale)
                                                                @php
                                                                    $totalAmount += $creditSale->amount;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-center">{{ $creditSale->id }}</td>
                                                                    <td class="text-center">{{ date('d-m-Y', strtotime($creditSale->transasction_date)) }}</td>
                                                                    <td class="text-center">{{ $creditSale->product_id ?? 'Unknown' }}</td>
                                                                    <td class="text-center">{{ number_format($creditSale->quantity, 2) }} <small class="text-muted">ltr</small></td>
                                                                    <td class="text-center">Rs {{ number_format($creditSale->rate, 2) }}</td>
                                                                    <td class="text-center">Rs {{ number_format($creditSale->amount) }}</td>
                                                                    <td class="text-center">{{ $creditSale->vehicle_id ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach

                                                        @endif
                                                    </tbody>
                                                    @if(isset($creditSales) && count($creditSales) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td colspan="5" class="text-center">Total</td>
                                                                <td class="text-center">Rs {{ number_format($totalAmount) }}</td>
                                                                <td class="text-center">-</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cash Transactions Tab -->
                            <div class="tab-pane fade" id="cash-transactions" role="tabpanel">
                                <div class="p-4">
                                    <!-- Cash Receipts Card -->
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-cash-coin text-success me-2"></i>Cash Receipts</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Amount</th>
                                                            <th class="text-center">Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($cashReceipts) && count($cashReceipts) > 0)
                                                            @php
                                                                $totalAmount = 0;
                                                            @endphp
                                                            @foreach($cashReceipts as $receipt)
                                                                @php
                                                                    $totalAmount += $receipt->amount;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-center">{{ $receipt->tid }}</td>
                                                                    <td class="text-center">{{ date('d-m-Y', strtotime($receipt->transaction_date)) }}</td>
                                                                    <td class="text-center">Rs {{ number_format($receipt->amount) }}</td>
                                                                    <td class="text-center">{{ $receipt->description ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    @if(isset($cashReceipts) && count($cashReceipts) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td colspan="2" class="text-center">Total</td>
                                                                <td class="text-center">Rs {{ number_format($totalAmount) }}</td>
                                                                <td class="text-center">-</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cash Payments Card -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-wallet text-danger me-2"></i>Cash Payments</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Amount</th>
                                                            <th class="text-center">Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($cashPayments) && count($cashPayments) > 0)
                                                            @php
                                                                $totalAmount = 0;
                                                            @endphp
                                                            @foreach($cashPayments as $payment)
                                                                @php
                                                                    $totalAmount += $payment->amount;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-center">{{ $payment->tid }}</td>
                                                                    <td class="text-center">{{ date('d-m-Y', strtotime($payment->transaction_date)) }}</td>
                                                                    <td class="text-center">Rs {{ number_format($payment->amount) }}</td>
                                                                    <td class="text-center">{{ $payment->description ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    @if(isset($cashPayments) && count($cashPayments) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td colspan="2" class="text-center">Total</td>
                                                                <td class="text-center">Rs {{ number_format($totalAmount) }}</td>
                                                                <td class="text-center">-</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Transactions Tab -->
                            <div class="tab-pane fade" id="bank-transactions" role="tabpanel">
                                <div class="p-4">
                                    <!-- Bank Receivings Card -->
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-bank text-primary me-2"></i>Bank Receivings</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Amount</th>
                                                            <th class="text-center">Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($bankReceivings) && count($bankReceivings) > 0)
                                                            @php
                                                                $totalAmount = 0;
                                                            @endphp
                                                            @foreach($bankReceivings as $receiving)
                                                                @php
                                                                    $totalAmount += $receiving->amount;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-center">{{ $receiving->tid }}</td>
                                                                    <td class="text-center">{{ date('d-m-Y', strtotime($receiving->transaction_date)) }}</td>
                                                                    <td class="text-center">Rs {{ number_format($receiving->amount) }}</td>
                                                                    <td class="text-center">{{ $receiving->description ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    @if(isset($bankReceivings) && count($bankReceivings) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td colspan="2" class="text-center">Total</td>
                                                                <td class="text-center">Rs {{ number_format($totalAmount) }}</td>
                                                                <td class="text-center">-</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bank Payments Card -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-bank2 text-warning me-2"></i>Bank Payments</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Amount</th>
                                                            <th class="text-center">Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($bankPayments) && count($bankPayments) > 0)
                                                            @php
                                                                $totalAmount = 0;
                                                            @endphp
                                                            @foreach($bankPayments as $payment)
                                                                @php
                                                                    $totalAmount += $payment->amount;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-center">{{ $payment->tid }}</td>
                                                                    <td class="text-center">{{ date('d-m-Y', strtotime($payment->transaction_date)) }}</td>
                                                                    <td class="text-center">Rs {{ number_format($payment->amount) }}</td>
                                                                    <td class="text-center">{{ $payment->description ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    @if(isset($bankPayments) && count($bankPayments) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td colspan="2" class="text-center">Total</td>
                                                                <td class="text-center">Rs {{ number_format($totalAmount) }}</td>
                                                                <td class="text-center">-</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Journal Vouchers Tab -->
                            <div class="tab-pane fade" id="journal" role="tabpanel">
                                <div class="p-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="mb-0"><i class="bi bi-journal text-info me-2"></i>Journal Vouchers</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover account-history-table" style="width:100%">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Debit</th>
                                                            <th class="text-center">Credit</th>
                                                            <th class="text-center">Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(isset($journalEntries) && count($journalEntries) > 0)
                                                            @php
                                                                $totalDebit = 0;
                                                                $totalCredit = 0;
                                                            @endphp
                                                            @foreach($journalEntries as $entry)
                                                                <tr>
                                                                    <td class="text-center">{{ $entry->id }}</td>
                                                                    <td class="text-center">{{ date('d-m-Y', strtotime($entry->transaction_date)) }}</td>
                                                                    <td class="text-center">
                                                                        @if($entry->debit_credit == 2)
                                                                            @php $totalDebit += $entry->amount; @endphp
                                                                            Rs {{ number_format($entry->amount) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($entry->debit_credit == 1)
                                                                            @php $totalCredit += $entry->amount; @endphp
                                                                            Rs {{ number_format($entry->amount) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">{{ $entry->description ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                    @if(isset($journalEntries) && count($journalEntries) > 0)
                                                        <tfoot class="table-primary">
                                                            <tr class="fw-bold">
                                                                <td colspan="2" class="text-center">Total</td>
                                                                <td class="text-center">Rs {{ number_format($totalDebit) }}</td>
                                                                <td class="text-center">Rs {{ number_format($totalCredit) }}</td>
                                                                <td class="text-center">-</td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-primary text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    Please select an account from the dropdown above to view the comprehensive transaction history.
                </div>
            </div>
        </div>
    @endif
</div>
@endpermission
@endsection

@push('scripts')
<script src="{{ asset('js/account-history.js') }}"></script>
@endpush
