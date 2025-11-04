@extends('admin.layout.master')

@section('title', 'Nozzle Sales')
@section('description', 'Manage nozzle-based sales')

@section('content')
    @permission('sales.nozzle.view')
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h3 class="mb-0"><i class="bi bi-fuel-pump text-primary me-2"></i>Nozzle Sales</h3>
                    <p class="text-muted mb-0">Record sales for each nozzle with readings</p>
                </div>
            </div>

            <!-- Form moved to modal -->

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Sales</h5>
                            <div class="d-flex gap-2">
                                @permission('sales.nozzle.create')
                                    <button type="button" id="openNozzleSalesModalBtn"
                                        class="btn btn-primary d-flex align-items-center">
                                        <i class="bi bi-plus-circle me-2"></i> Add Nozzle Sales
                                    </button>
                                @endpermission
                            </div>
                        </div>
                        <div class="card-body p-0 pt-0">
                            <div class="table-responsive">
                                <table id="salesTable" class="table table-hover table-bordered align-middle mb-0 text-center"
                                    style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 text-center">#</th>
                                            <th class="ps-3 text-center">Sales Date</th>
                                            <th class="ps-3 text-center">Product</th>
                                            <th class="ps-3 text-center">Opening Stock</th>
                                            <th class="ps-3 text-center">Quantity</th>
                                            <th class="ps-3 text-center">Rate</th>
                                            <th class="ps-3 text-center">Amount</th>
                                            <th class="ps-3 text-center">Nozzle</th>
                                            <th class="ps-3 text-center">Closing Reading</th>
                                            <th class="ps-3 text-center">Test Sales</th>
                                            <th class="ps-3 text-center">Tank</th>
                                            <th class="ps-3 text-center">Profit/Loss</th>
                                            <th class="ps-3 text-center">Notes</th>
                                            <th class="ps-3 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sales as $sale)
                                            @php
                                                $product = \App\Models\Management\Product::find($sale->product_id);
                                                $nozzle = $sale->nozzle_id
                                                    ? \App\Models\Management\Nozzle::find($sale->nozzle_id)
                                                    : null;
                                                $tank = \App\Models\Management\Tank::find($sale->tank_id);
                                            @endphp
                                            <tr>
                                                <td class="ps-3 text-center">{{ $sale->id }}</td>
                                                <td class="ps-3 text-center">{{ date('d-m-Y', strtotime($sale->create_date)) }}
                                                </td>
                                                <td class="ps-3 text-center">{{ $product?->name ?? 'Not found / deleted' }}</td>
                                                <td class="ps-3 text-center">{{ $sale->previous_stock }}</td>
                                                <td class="ps-3 text-center">{{ $sale->quantity }}</td>
                                                <td class="ps-3 text-center">Rs {{ $sale->rate }}</td>
                                                <td class="ps-3 text-center">Rs {{ number_format($sale->amount) }}</td>
                                                <td class="ps-3 text-center">{{ $nozzle?->name ?? 'Not found' }}</td>
                                                <td class="ps-3 text-center">{{ $sale->closing_reading }}</td>
                                                <td class="ps-3 text-center">{{ $sale->test_sales }}</td>
                                                <td class="ps-3 text-center">{{ $tank?->tank_name ?? 'Not found' }}</td>
                                                <td class="ps-3 text-center">Rs {{ number_format($sale->profit, 2, '.', ',') }}</td>
                                                <td class="ps-3 text-center">{{ $sale->notes }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        @php
                                                            $isLast = $sales_detail->contains(function ($value) use (
                                                                $sale,
                                                            ) {
                                                                return $value->product_id == $sale->product_id &&
                                                                    $value->last_row_id == $sale->id;
                                                            });
                                                        @endphp
                                                        @if ($isLast)
                                                            @permission('sales.nozzle.delete')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger delete-sales-btn"
                                                                    data-id="{{ $sale->id }}">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            @endpermission
                                                        @endif
                                                    </div>
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

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Sales Summary</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0" id="sales_card">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 text-center" scope="col">Product Name</th>
                                            <th class="ps-3 text-center" scope="col">Quantity</th>
                                            <th class="ps-3 text-center" scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($totalstock = 0)
                                        @php($totalamount = 0)
                                        @foreach ($salesSummary as $row)
                                            @php($totalstock += $row->total_quantity)
                                            @php($totalamount += $row->total_amount)
                                            <tr>
                                                <td>{{ $row->product_name }}</td>
                                                <td>{{ number_format($row->total_quantity) }}</td>
                                                <td>Rs {{ number_format($row->total_amount) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-primary fw-bold">
                                            <td>Total</td>
                                            <td><b>{{ number_format($totalstock) }}</b></td>
                                            <td><b>Rs {{ number_format($totalamount) }}</b></td>
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
                    <button id="printReportBtn" class="btn btn-primary px-4">
                        <i class="bi bi-printer me-2"></i>Print Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Nozzle Sales Modal -->
        <div class="modal fade" id="nozzleSalesModal" tabindex="-1" aria-labelledby="nozzleSalesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow" id="modalBody">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="nozzleSalesModalLabel"><i class="bi bi-fuel-pump me-2"></i>Add Nozzle Sales
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <form id="addSalesForm_pump">
                            <div class="row mb-3 gx-2">
                                <div class="col-12 col-md-3">
                                    <label for="sale_date" class="form-label">Sale Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-calendar"></i>
                                        </span>
                                        <input class="form-control border-start-0" id="sale_date" type="date"
                                            value="{{ $dateLock }}" disabled required max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="product_id" class="form-label">Select Product</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-funnel"></i>
                                        </span>
                                        <select name="product_id" id="product_id" class="form-select border-start-0 searchable-dropdown-modal"
                                            required>
                                            <option selected disabled>Choose product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 d-flex align-items-end">
                                    <div>
                                        <p class="mb-1" id="current_rate">&nbsp;</p>
                                        <input type="hidden" id="sale_rate">
                                        <select name="customer_id" id="customer_id" required style="display:none;">
                                            <option selected value="7" data-name="cash" data-type="7">Cash</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3" id="nozzles_div"></div>

                            <div class="row gx-2 align-items-end">
                                <div class="col-12 col-md-8">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea rows="1" class="form-control" name="notes" id="notes" required></textarea>
                                </div>
                                <div class="col-12 col-md-3 ms-auto d-grid">
                                    <button type="submit" id="add_sales_btn" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
    </style>
@endsection

@push('scripts')
    <script src="{{ asset('js/nozzle-sales-ajax.js') }}?v=1.1"></script>
@endpush
