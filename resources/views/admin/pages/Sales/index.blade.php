@extends('admin.layout.master')

@section('title', 'Manage Purchase')
@section('description', 'Manage System Purchase')

@section('content')
@permission('sales.view')
{{-- @php
    $HSDAmount = 0;
    $SuperAmount = 0;
    $HSDQuantity = 0;
    $SuperQuantity = 0;
    foreach($purchases as $key => $purchase){
        $productName = \App\Models\Management\Product::find($purchase->product_id)->name;
        if($productName=="Super"){
            $SuperAmount += $purchase->total_amount;
            $SuperQuantity += $purchase->stock;
        }elseif($productName=="HSD"){
            $HSDAmount += $purchase->total_amount;
            $HSDQuantity += $purchase->stock;
        }
    }
@endphp --}}
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-cart-plus text-primary me-2"></i>Sales</h3>
            <p class="text-muted mb-0">Manage Sales details</p>
        </div>
    </div>
    <!-- Purchase Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Sales</h5>
                    @permission('sales.create')
                    <button type="button" id="addNewSalesBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Sales
                    </button>
                    @endpermission
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table id="salesTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th class="ps-3">Sales Date</th>
                                    <th class="ps-3">Vendor</th>
                                    <th class="ps-3">Product</th>
                                    <th class="ps-3">Tank Lorry</th>
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
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $key => $sale)
                                <tr>
                                    <td class="ps-3 text-start">{{ $sale->id }}</td>
                                    <td class="ps-3 text-start">
                                        {{ date('d-m-Y', strtotime($sale->create_date)) }}
                                    </td>
                                    @php
                                        $vendor = $sale->getVendorByType($sale->vendor_type, $sale->customer_id);
                                        $productName = \App\Models\Management\Product::find($sale->product_id)->name;
                                        $tankLorry = \App\Models\Management\TankLari::find($sale->tank_lari_id)?->larry_name ?? null;
                                        $tankName = \App\Models\Management\Tank::find($sale->tank_id)->tank_name;
                                    @endphp
                                    <td class="ps-3 text-start">
                                        <span class="badge bg-primary">{{ $vendor->vendor_type }}</span>
                                    </td>
                                    <td class="ps-3 text-start">{{ $productName }}</td>
                                    <td class="ps-3 text-start">
                                        @if($tankLorry)
                                            {{ $tankLorry }}
                                        @else
                                            <span class="text-danger" title="Tank Lorry Not Found"><i class="fas fa-exclamation-circle me-1"></i> Not Found</span>
                                        @endif
                                    </td>
                                    <td class="ps-3 text-start">{{ number_format($sale->previous_stock, 0, '', ',') }} <small>ltr</small></td>
                                    <td class="ps-3 text-start">{{ number_format($sale->quantity, 0, '', ',') }} <small>ltr</small></td>
                                    <td class="ps-3 text-start">{{ number_format($sale->previous_stock - $sale->quantity, 0, '', ',') }} <small>ltr</small></td>
                                    <td class="ps-3 text-start"><small>Rs</small> {{ $sale->rate}}</td>
                                    <td class="ps-3 text-start"><small>Rs</small> {{ $sale->amount }}</td>
                                    <td class="ps-3 text-start">{{ $sale->freight == 0 ? 'No' : 'Yes' }}</td>
                                    <td class="ps-3 text-start">{{ $sale->freight_charges }}</td>
                                    <td class="ps-3 text-center">{{ $tankName }}</td>
                                    <td class="ps-3 text-start">
                                        <span class="badge bg-primary">{{ $sale->sales_type == 1 ? 'Goddam' : 'Direct' }}</span>
                                    </td>
                                    <td class="ps-3 text-start"><small>Rs</small> {{ number_format(round($sale->profit), 0, '', ',') }}</td>
                                    <td class="text-center">
                                        {{ $sale->notes }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @permission('sales.delete')
                                            <button type="button" class="btn btn-sm btn-danger delete-sales-btn"
                                                data-id="{{ $sale->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endpermission
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


    <!-- Sales Cards Section -->
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
                                @php
                                    $totalstock = 0;
                                    $totalamount = 0;
                                @endphp
                                @foreach($salesSummary as $sale)
                                    @php
                                        $totalstock += $sale->total_quantity;
                                        $totalamount += $sale->total_amount;
                                    @endphp
                                    <tr>
                                        <td>{{ $sale->product_name }}</td>
                                        <td>{{ number_format($sale->total_quantity) }}</td>
                                        <td>Rs {{ number_format($sale->total_amount) }}</td>
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

    <!-- Print Button -->
    <div class="row mt-4 mb-4">
        <div class="col-12 text-center">
            <button id="printReportBtn" class="btn btn-primary px-4">
                <i class="bi bi-printer me-2"></i>Print Report
            </button>
        </div>
    </div>
</div>
@endpermission


<style>
    .avatar-sm {
        width: 36px;
        height: 36px;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-soft-primary {
        background-color: rgba(65, 84, 241, 0.2) !important;
    }
    .bg-soft-success {
        background-color: rgba(10, 187, 135, 0.2) !important;
    }
    .bg-soft-warning {
        background-color: rgba(255, 171, 0, 0.2) !important;
    }
    .bg-soft-info {
        background-color: rgba(2, 168, 238, 0.2) !important;
    }
    .bg-soft-danger {
        background-color: rgba(242, 78, 30, 0.2) !important;
    }
    .bg-gradient-primary {
        background: linear-gradient(to right, #4154f1, #2e40c8) !important;
        border-color: #4154f1 !important;
    }
    .bg-gradient-danger {
        background: linear-gradient(to right, #f14141, #c82e2e) !important;
        border-color: #f14141 !important;
    }
    .form-label {
        margin-bottom: 0.3rem;
        font-weight: 500;
        color: #444;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4154f1;
        box-shadow: 0 0 0 0.25rem rgba(65, 84, 241, 0.1);
    }
    .modal-content {
        border-radius: 0.5rem;
    }
    .modal-header {
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .input-group-text {
        color: #6c757d;
    }
    .btn-primary {
        background-color: #4154f1;
        border-color: #4154f1;
    }
    .btn-primary:hover, .btn-primary:focus {
        background-color: #ffffff;
        border-color: #3a4cd8;
    }
    .btn-outline-primary {
        color: #4154f1;
        border-color: #4154f1;
    }
    .btn-outline-primary:hover {
        background-color: #4154f1;
        border-color: #4154f1;
    }
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
        border-radius: 30px;
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
    .btn.btn-primary:hover {
        background-color: #ffffff;
        border-color: #4154f1;
        color: #4154f1;
    }
    .btn-info {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
    }
    .btn-info:hover {
        background-color: #0bacdb;
        border-color: #0bacdb;
    }
    #measurements_div {
        font-size: 14px;
        line-height: 1.8;
    }
    #measurements_div strong {
        color: #495057;
        display: inline-block;
        min-width: 150px;
    }
</style>
@endsection

@push('scripts')
<script src="{{ asset('js/sales-ajax.js') }}"></script>
@endpush

