@extends('admin.layout.master')
@section('title', 'General Search | Stock Management')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                            <h5 class="mb-0 me-md-3">General Search</h5>
                            <form action="{{ route('admin.general-search.index') }}" method="get" class="flex-grow-1">
                                <div class="input-group">
                                    <input type="text" name="q" value="{{ old('q', $q ?? '') }}" class="form-control"
                                           placeholder="Enter exact amount (e.g., 12500)">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-search me-1"></i> Search
                                    </button>
                                </div>
                            </form>
                        </div>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.925rem;">
                            Type a transaction amount to find matching records in Sales, Purchases, Journal Vouchers, Transactions, and Credit Sales.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if(($q ?? '') === '')
            <div class="row">
                <div class="col-12 col-lg-10">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-2">How it works</h6>
                            <ul class="mb-0 text-muted">
                                <li>Search by exact amount to match the amount field in each module.</li>
                                <li>Results are grouped by module with most recent first.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="gsearchTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-tab-pane" type="button" role="tab" aria-controls="sales-tab-pane" aria-selected="true">
                                        Sales ({{ $salesResults->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="purchase-tab" data-bs-toggle="tab" data-bs-target="#purchase-tab-pane" type="button" role="tab" aria-controls="purchase-tab-pane" aria-selected="false">
                                        Purchases ({{ $purchaseResults->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="journal-tab" data-bs-toggle="tab" data-bs-target="#journal-tab-pane" type="button" role="tab" aria-controls="journal-tab-pane" aria-selected="false">
                                        Journal Vouchers ({{ $journalResults->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions-tab-pane" type="button" role="tab" aria-controls="transactions-tab-pane" aria-selected="false">
                                        Transactions ({{ $transactionResults->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="credit-sales-tab" data-bs-toggle="tab" data-bs-target="#credit-sales-tab-pane" type="button" role="tab" aria-controls="credit-sales-tab-pane" aria-selected="false">
                                        Credit Sales ({{ $creditSalesResults->count() }})
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content pt-3" id="gsearchTabsContent">
                                <div class="tab-pane fade show active" id="sales-tab-pane" role="tabpanel" aria-labelledby="sales-tab" tabindex="0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle mb-0">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Tank</th>
                                                <th>Quantity</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                                <th>Vendor</th>
                                                <th>Vendor Type</th>
                                                <th>Tank Lorry</th>
                                                <th>Freight</th>
                                                <th>Freight Charges</th>
                                                <th>Notes</th>
                                                <th>Sale Date</th>
                                                <th>Entered By</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($salesResults as $row)
                                                <tr>
                                                    <td>{{ $row->id }}</td>
                                                    <td>{{ optional($row->product)->name }}</td>
                                                    <td>{{ optional($row->tank)->tank_name }}</td>
                                                    <td>{{ $row->quantity }}</td>
                                                    <td>{{ number_format((float) $row->rate, 2) }}</td>
                                                    <td>{{ number_format((float) $row->amount, 2) }}</td>
                                                    <td>{{ $row->vendor_name_display }}</td>
                                                    <td>{{ $row->vendor_type_display }}</td>
                                                    <td>{{ optional($row->tankLari)->vehicle_no ?? $row->tank_lari_id }}</td>
                                                    <td>{{ $row->freight == 1 ? 'Yes' : 'No' }}</td>
                                                    <td>{{ number_format((float) $row->freight_charges, 2) }}</td>
                                                    <td>{{ $row->notes }}</td>
                                                    <td>{{ $row->create_date }}</td>
                                                    <td>{{ $row->entered_by_name }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="13" class="text-center text-muted">No sales found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="purchase-tab-pane" role="tabpanel" aria-labelledby="purchase-tab" tabindex="0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle mb-0">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Tank</th>
                                                <th>Vendor</th>
                                                <th>Vendor Type</th>
                                                <th>Stock</th>
                                                <th>Rate</th>
                                                <th>Total Amount</th>
                                                <th>Vehicle No</th>
                                                <th>Driver No</th>
                                                <th>Terminal</th>
                                                <th>Comments</th>
                                                <th>Purchase Date</th>
                                                <th>Entered By</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($purchaseResults as $row)
                                                <tr>
                                                    <td>{{ $row->id }}</td>
                                                    <td>{{ optional($row->product)->name }}</td>
                                                    <td>{{ optional($row->tank)->tank_name }}</td>
                                                    <td>{{ $row->vendor_name_display }}</td>
                                                    <td>{{ $row->vendor_type_display }}</td>
                                                    <td>{{ $row->stock }}</td>
                                                    <td>{{ number_format((float) $row->rate, 2) }}</td>
                                                    <td>{{ number_format((float) $row->total_amount, 2) }}</td>
                                                    <td>{{ $row->vehicle_no }}</td>
                                                    <td>{{ $row->driver_no }}</td>
                                                    <td>{{ $row->terminal_id }}</td>
                                                    <td>{{ $row->comments }}</td>
                                                    <td>{{ $row->purchase_date }}</td>
                                                    <td>{{ $row->entered_by_name }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="13" class="text-center text-muted">No purchases found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="journal-tab-pane" role="tabpanel" aria-labelledby="journal-tab" tabindex="0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle mb-0">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Voucher ID</th>
                                                <th>Vendor Type</th>
                                                <th>Vendor</th>
                                                <th>Amount</th>
                                                <th>RS Type</th>
                                                <th>Description</th>
                                                <th>Transaction Date</th>
                                                <th>Entered By</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($journalResults as $row)
                                                <tr>
                                                    <td>{{ $row->id }}</td>
                                                    <td>{{ $row->voucher_id }}</td>
                                                    <td>{{ $row->vendor_type_display }}</td>
                                                    <td>{{ $row->vendor_name_display }}</td>
                                                    <td>{{ number_format((float) $row->amount, 2) }}</td>
                                                    <td>
                                                        @if($row->debit_credit == 1)
                                                            <span class="badge bg-warning text-dark">credit</span>
                                                        @elseif($row->debit_credit == 2)
                                                            <span class="badge bg-success">debit</span>
                                                        @else
                                                            <span class="badge bg-secondary">n/a</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $row->description }}</td>
                                                    <td>{{ optional($row->transaction_date)->format('Y-m-d') ?? $row->transaction_date }}</td>
                                                    <td>{{ $row->entered_by_name }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">No journal vouchers found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="transactions-tab-pane" role="tabpanel" aria-labelledby="transactions-tab" tabindex="0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle mb-0">
                                            <thead>
                                            <tr>
                                                <th>TID</th>
                                                <th>Vendor Type</th>
                                                <th>Vendor</th>
                                                <th>Transaction Type</th>
                                                <th>Payment Type</th>
                                                <th>Bank</th>
                                                <th>Amount</th>
                                                <th>Customer Balance</th>
                                                <th>Description</th>
                                                <th>Transaction Date</th>
                                                <th>Entered By</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($transactionResults as $row)
                                                <tr>
                                                    <td>{{ $row->tid }}</td>
                                                    <td>{{ $row->vendor_type_display }}</td>
                                                    <td>{{ $row->vendor_name_display }}</td>
                                                    <td>{{ $transactionTypeMap[$row->transaction_type] ?? $row->transaction_type }}</td>
                                                    <td>{{ $paymentTypeMap[$row->payment_type] ?? $row->payment_type }}</td>
                                                    <td>{{ optional($row->bank)->name ?? $row->bank_name }}</td>
                                                    <td>{{ number_format((float) $row->amount, 2) }}</td>
                                                    <td>{{ $row->customer_balance }}</td>
                                                    <td>{{ $row->description }}</td>
                                                    <td>{{ $row->transaction_date }}</td>
                                                    <td>{{ $row->entered_by_name }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted">No transactions found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="credit-sales-tab-pane" role="tabpanel" aria-labelledby="credit-sales-tab" tabindex="0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle mb-0">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Transaction ID</th>
                                                <th>Transaction Type</th>
                                                <th>Payment Type</th>
                                                <th>Product</th>
                                                <th>Tank</th>
                                                <th>Vendor</th>
                                                <th>Vendor Type</th>
                                                <th>Vehicle</th>
                                                <th>Quantity</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                                <th>Notes</th>
                                                <th>Invoice No</th>
                                                <th>Transaction Date</th>
                                                <th>Entered By</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($creditSalesResults as $row)
                                                <tr>
                                                    <td>{{ $row->id }}</td>
                                                    <td>{{ $row->transaction_id }}</td>
                                                    <td>{{ $transactionTypeMap[$row->transaction_type] ?? $row->transaction_type }}</td>
                                                    <td>{{ $paymentTypeMap[$row->payment_type] ?? $row->payment_type }}</td>
                                                    <td>{{ optional($row->product)->name }}</td>
                                                    <td>{{ optional($row->tank)->tank_name }}</td>
                                                    <td>{{ $row->vendor_name_display }}</td>
                                                    <td>{{ $row->vendor_type_display }}</td>
                                                    <td>{{ optional($row->vehicle)->vehicle_no ?? $row->vehicle_id }}</td>
                                                    <td>{{ number_format((float) $row->quantity, 2) }}</td>
                                                    <td>{{ number_format((float) $row->rate, 2) }}</td>
                                                    <td>{{ number_format((float) $row->amount, 2) }}</td>
                                                    <td>{{ $row->notes }}</td>
                                                    <td>{{ $row->invoice_no }}</td>
                                                    <td>{{ optional($row->transasction_date)->format('Y-m-d') ?? $row->transasction_date }}</td>
                                                    <td>{{ $row->entered_by_name }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="15" class="text-center text-muted">No credit sales found.</td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection


