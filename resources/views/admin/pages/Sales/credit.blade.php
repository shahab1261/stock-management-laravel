@extends('admin.layout.master')

@section('title', 'Credit Sales')
@section('description', 'Manage credit sales transactions')

@section('content')
    @permission('sales.credit.view')
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h3 class="mb-0"><i class="bi bi-credit-card-2-front text-primary me-2"></i>Credit Sales</h3>
                    <p class="text-muted mb-0">Record credit sales transactions</p>
                </div>
            </div>

            <!-- Credit Sales Form Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Credit Sale</h5>
                                <p class="mb-0 text-muted">Current Balance: Rs {{ number_format($currentCash) }}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="credit_sales_form">
                                <div class="row mb-3 gx-2">
                                    <div class="col-12 col-md-2">
                                        <label for="transaction_date" class="form-label">Date</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-calendar"></i>
                                            </span>
                                            <input type="date" class="form-control border-start-0" id="transaction_date"
                                                   value="{{ $dateLock }}" disabled required max="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="product_id" class="form-label">Select Product</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-funnel"></i>
                                            </span>
                                            <select name="product_id" id="product_id" class="form-select border-start-0 searchable-dropdown" required>
                                                <option selected disabled>Choose product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label class="form-label">Select Tank</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-bucket"></i>
                                            </span>
                                            <select class="form-select border-start-0" id="selected_tank" required>
                                                {{-- <option value="" selected disabled>Choose tank</option> --}}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="customer_id" class="form-label">Select Customer</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-person"></i>
                                            </span>
                                            <select name="customer_id" id="customer_id" class="form-select border-start-0 searchable-dropdown" required>
                                                <option selected disabled>Choose customer</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-type="2">{{ $customer->name }} (Customer)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="tank_lari_id" class="form-label">Customer's Vehicle</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-truck"></i>
                                            </span>
                                            <select name="tank_lari_id" id="tank_lari_id" class="form-select border-start-0 searchable-dropdown" required>
                                                <option value="" selected disabled>Choose vehicle</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3 gx-2">
                                    <div class="col-12 col-md-2">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-calculator"></i>
                                            </span>
                                            <input type="number" class="form-control border-start-0" min="0.01" required
                                                   name="quantity" id="quantity" placeholder="0.00" step="0.01">
                                            <span class="input-group-text">Ltr</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="rate" class="form-label">Rate</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">Rs</span>
                                            <input type="number" min="0.01" pattern="^[0-9]+(\.[0-9]+)?$" required
                                                   class="form-control border-start-0" name="rate" id="rate"
                                                   placeholder="0.00" step="0.01" disabled>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="amount" class="form-label">Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">Rs</span>
                                            <input class="form-control border-start-0" type="text" readonly id="amount" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="transaction_description" class="form-label">Description</label>
                                        <textarea class="form-control" rows="1" id="transaction_description"
                                                  placeholder="Enter transaction description" required></textarea>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="invoice_no" class="form-label">Invoice No</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-receipt"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" name="invoice_no" id="invoice_no"
                                                   placeholder="Enter invoice number" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button Row -->
                                <div class="row mb-3">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button class="btn btn-primary px-4" type="submit" id="transaction_btn">
                                            <i class="bi bi-check-circle me-2"></i>Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credit Sales Table -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Credit Sales Records</h5>
                            <div class="d-flex gap-2">
                                <button type="button" id="printCreditSalesTableBtn" class="btn btn-primary d-flex align-items-center">
                                    <i class="bi bi-printer me-2"></i> Print
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle mb-0 text-center" id="creditSalesTable" style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 text-center">#</th>
                                            <th class="ps-3 text-center">Date</th>
                                            <th class="ps-3 text-center">Invoice No</th>
                                            <th class="ps-3 text-center">Vendor <small style="font-size: 10px">(Debit)</small></th>
                                            <th class="ps-3 text-center">Product</th>
                                            <th class="ps-3 text-center">Tank Lorry</th>
                                            <th class="ps-3 text-center">Quantity</th>
                                            <th class="ps-3 text-center">Rate</th>
                                            <th class="ps-3 text-center">Amount</th>
                                            <th class="ps-3 text-center">Description</th>
                                            <th class="ps-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalAmount = 0;
                                        @endphp
                                        @foreach ($creditSales as $creditSale)
                                            @php
                                                $product = \App\Models\Management\Product::find($creditSale->product_id);
                                                $tankLari = \App\Models\Management\TankLari::find($creditSale->vehicle_id);
                                                $vendor = $creditSale->getVendorByType($creditSale->vendor_type, $creditSale->vendor_id);
                                                $totalAmount += $creditSale->amount;
                                            @endphp
                                            <tr>
                                                <td class="ps-3 text-center">{{ $creditSale->id }}</td>
                                                <td class="ps-3 text-center">{{ date('d-m-Y', strtotime($creditSale->transasction_date)) }}</td>
                                                <td class="ps-3 text-center">
                                                    @if($creditSale->invoice_no)
                                                        <span class="badge bg-primary">{{ $creditSale->invoice_no }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="ps-3 text-center">
                                                    {{ $vendor->vendor_name }}
                                                    <span class="badge bg-secondary">{{ $vendor->vendor_type }}</span>
                                                </td>
                                                <td class="ps-3 text-center">{{ $product?->name ?? 'Not found / deleted' }}</td>
                                                <td class="ps-3 text-center">
                                                    @if(!empty($tankLari))
                                                        {{ $tankLari->larry_name }}
                                                    @else
                                                        Not found
                                                    @endif
                                                </td>
                                                <td class="ps-3 text-center">{{ $creditSale->quantity }} <small>ltr</small></td>
                                                <td class="ps-3 text-center">Rs {{ $creditSale->rate }}</td>
                                                <td class="ps-3 text-center">Rs {{ number_format($creditSale->amount) }}</td>
                                                <td class="ps-3 text-center">{{ $creditSale->notes }}</td>
                                                <td class="ps-3 text-center">
                                                    @permission('sales.credit.delete')
                                                        <button class="btn btn-sm btn-danger delete-credit-sale-btn"
                                                                data-ledgerpurchasetype="12" data-id="{{ $creditSale->id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endpermission
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Summary -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Credit Sales Summary</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0" id="sales_summary_card">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 text-center" scope="col">Product Name</th>
                                            <th class="ps-3 text-center" scope="col">Quantity</th>
                                            <th class="ps-3 text-center" scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($totalStock = 0)
                                        @php($totalSummaryAmount = 0)
                                        @foreach ($salesSummary as $row)
                                            @php($totalStock += $row->total_quantity)
                                            @php($totalSummaryAmount += $row->total_amount)
                                            <tr>
                                                <td class="ps-3">{{ $row->product_name }}</td>
                                                <td class="ps-3 text-center">{{ number_format($row->total_quantity) }}</td>
                                                <td class="ps-3 text-center">Rs {{ number_format($row->total_amount) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-primary fw-bold">
                                            <td class="ps-3">Total</td>
                                            <td class="ps-3 text-center"><b>{{ number_format($totalStock) }}</b></td>
                                            <td class="ps-3 text-center"><b>Rs {{ number_format($totalSummaryAmount) }}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 mb-4">
                <div class="col-12 text-center">
                    <button id="printCreditSalesReportBtn" class="btn btn-primary px-4">
                        <i class="bi bi-printer me-2"></i>Print Report
                    </button>
                </div>
            </div>
        </div>
    @endpermission

    <style>
        label {
            font-size: 11px;
        }

        .dataTables_wrapper {
            padding-top: 15px;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #6c757d;
            padding: 8px;
        }

        .dataTables_wrapper .dataTables_length {
            padding-left: 15px;
        }

        .dataTables_wrapper .dataTables_info {
            padding-left: 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-primary:hover {
            /* background-color: #0b5ed7; */
            border-color: #0a58ca;
        }

        .card {
            border-radius: 0.5rem;
        }

        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }
    </style>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/credit-sales.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/credit-sales-ajax.js') }}?v=0.2"></script>
@endpush
