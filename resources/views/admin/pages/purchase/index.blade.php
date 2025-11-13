@extends('admin.layout.master')

@section('title', 'Manage Purchase')
@section('description', 'Manage System Purchase')

@section('content')
    @php
        $HSDAmount = 0;
        $SuperAmount = 0;
        $HSDQuantity = 0;
        $SuperQuantity = 0;
        foreach ($purchases as $key => $purchase) {
            $productName = \App\Models\Management\Product::find($purchase->product_id)->name;
            if ($productName == 'Super') {
                $SuperAmount += $purchase->total_amount;
                $SuperQuantity += $purchase->stock;
            } elseif ($productName == 'HSD') {
                $HSDAmount += $purchase->total_amount;
                $HSDQuantity += $purchase->stock;
            }
        }
    @endphp
    @permission('purchase.view')
        <div class="container-fluid py-4">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h3 class="mb-0"><i class="bi bi-cart-check text-primary me-2"></i>Purchase</h3>
                    <p class="text-muted mb-0">Manage purchase details</p>
                </div>
            </div>
            <!-- Purchase Table Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0"><i class="bi bi-cart-check me-2"></i>Purchase</h5>
                            @permission('purchase.create')
                                <button type="button" id="addNewPurchaseBtn" class="btn btn-primary d-flex align-items-center">
                                    <i class="bi bi-plus-circle me-2"></i> Add New Purchase
                                </button>
                            @endpermission
                        </div>
                        <div class="card-body p-0 pt-0">
                            <div class="table-responsive">
                                <table id="purchaseTable" class="table table-hover table-bordered table-bordered align-middle mb-0" style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">#</th>
                                            <th class="ps-3">Purchase Date</th>
                                            <th class="ps-3">Vendor</th>
                                            <th class="ps-3">Product</th>
                                            <th class="ps-3">Tank Lorry</th>
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
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchases as $key => $purchase)
                                            <tr>
                                                <td class="ps-3 text-start">{{ $key + 1 }}</td>
                                                <td class="ps-3 text-start">
                                                    {{ date('d-m-Y', strtotime($purchase->purchase_date)) }}
                                                </td>
                                                @php
                                                    $vendor = $purchase->getVendorByType(
                                                        $purchase->vendor_type,
                                                        $purchase->supplier_id,
                                                    );
                                                    $productName = \App\Models\Management\Product::find(
                                                        $purchase->product_id,
                                                    )->name;
                                                    $tankLorry = \App\Models\Management\TankLari::find(
                                                        $purchase->vehicle_no,
                                                    )->larry_name;
                                                    $driverName = \App\Models\Management\Drivers::find(
                                                        $purchase->driver_no,
                                                    )->driver_name;
                                                    $tankName = \App\Models\Management\Tank::find($purchase->tank_id)
                                                        ->tank_name;
                                                @endphp
                                                <td class="ps-3 text-start">{{ $vendor->vendor_name }}</td>
                                                <td class="ps-3 text-start">{{ $productName }}</td>
                                                <td class="ps-3 text-start">{{ $tankLorry ?? 'Not Found' }}</td>
                                                <td class="ps-3 text-start">
                                                    {{ number_format($purchase->previous_stock, 0, '', ',') }}
                                                    <small>ltr</small></td>
                                                <td class="ps-3 text-start">{{ number_format($purchase->stock, 0, '', ',') }}
                                                    <small>ltr</small></td>
                                                <td class="ps-3 text-start">
                                                    {{ number_format($purchase->previous_stock + $purchase->stock, 0, '', ',') }}
                                                    <small>ltr</small></td>
                                                <td class="ps-3 text-start"><small>Rs</small> {{ $purchase->rate }}</td>
                                                <td class="ps-3 text-start"><small>Rs</small> {{ $purchase->total_amount }}</td>
                                                <td class="ps-3 text-start">{{ $driverName }}</td>
                                                <td class="ps-3 text-start">{{ $purchase->sold_quantity }}</td>
                                                <td class="ps-3 text-start">{{ $tankName }}</td>
                                                <td class="ps-3 text-start">
                                                    @if ($purchase->image_path)
                                                        <a href="{{ $purchase->image_path }}" target="_blank"
                                                            class="text-primary">Receipt</a>
                                                    @else
                                                        <span class="text-danger">Not Uploaded</span>
                                                    @endif
                                                </td>
                                                <td class="ps-3 text-start">{{ $purchase->comments }}</td>
                                                <td class="ps-3 text-start"><small>Rs</small> {{ $purchase->rate_adjustment }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-primary show-chambers-btn"
                                                            data-id="{{ $purchase->id }}" data-name>
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        @permission('purchase.edit')
                                                        <a href="{{ route('purchase.edit-vendor', $purchase->id) }}" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        @endpermission
                                                        @if ($purchase->sold_quantity < 1)
                                                            @permission('purchase.delete')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger delete-purchase-btn"
                                                                    data-id="{{ $purchase->id }}"
                                                                    data-tank="{{ $purchase->tank_id }}"
                                                                    data-stock="{{ $purchase->stock }}">
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

            <!-- Purchase Summary Stats Section -->
            <div class="row mt-4">
                <!-- HSD Stats -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-gradient-primary text-white d-flex align-items-center py-3">
                            <i class="bi bi-fuel-pump-fill me-2 fs-5"></i>
                            <h5 class="card-title mb-0 fw-bold">HSD Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm bg-soft-primary rounded">
                                                <i class="bi bi-droplet-fill text-primary fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-6 text-muted mb-1">Total Quantity</h6>
                                            <h4 class="mb-0 fw-semibold" style="font-size: 30px;">
                                                {{ number_format($HSDQuantity, 0, '', ',') }} <small>ltr</small>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm bg-soft-success rounded">
                                                <i class="bi bi-currency-exchange text-success fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-6 text-muted mb-1">Total Amount</h6>
                                            <h4 class="mb-0 fw-semibold" style="font-size: 30px;">
                                                <small>Rs</small> {{ number_format($HSDAmount, 0, '', ',') }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Super Stats -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-gradient-danger text-white d-flex align-items-center py-3">
                            <i class="bi bi-fuel-pump me-2 fs-5"></i>
                            <h5 class="card-title mb-0 fw-bold">Super Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm bg-soft-danger rounded">
                                                <i class="bi bi-droplet-fill text-danger fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-6 text-muted mb-1">Total Quantity</h6>
                                            <h4 class="mb-0 fw-semibold" style="font-size: 30px;">
                                                {{ number_format($SuperQuantity, 0, '', ',') }} <small>ltr</small>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm bg-soft-success rounded">
                                                <i class="bi bi-currency-exchange text-success fs-4"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-6 text-muted mb-1">Total Amount</h6>
                                            <h4 class="mb-0 fw-semibold" style="font-size: 30px;">

                                                <small>Rs</small> {{ number_format($SuperAmount, 0, '', ',') }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-soft-warning rounded">
                                        <i class="bi bi-graph-up-arrow text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-6 text-muted mb-1">Average Rate</h6>
                                    <h4 class="mb-0 fw-semibold">
                                        @php
                                            $superAvgRate = $superQuantity > 0 ? $superAmount / $superQuantity : 0;
                                        @endphp
                                        <small>Rs</small> {{ number_format($superAvgRate, 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm bg-soft-info rounded">
                                        <i class="bi bi-percent text-info fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-6 text-muted mb-1">Percentage</h6>
                                    <h4 class="mb-0 fw-semibold">
                                        @php
                                            $superPercentage = $totalQuantity > 0 ? ($superQuantity / $totalQuantity) * 100 : 0;
                                        @endphp
                                        {{ number_format($superPercentage, 1) }}%
                                    </h4>
                                </div>
                            </div>
                        </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Cards Section -->
            {{-- <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-card-list me-2"></i>Purchase Summary</h5>
                    <button onclick="printDiv('purchasecard')" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="purchasecard">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalstock = 0;
                                    $totalamount = 0;
                                @endphp
                                @foreach ($purchaseStock as $sale)
                                    @php
                                        $totalstock += $sale->total_quantity;
                                        $totalamount += $sale->total_amount;
                                    @endphp
                                    <tr>
                                        <td>{{ $sale->product_name }}</td>
                                        <td>{{ number_format($sale->total_quantity, 0, '', ',') }}</td>
                                        <td>Rs {{ number_format($sale->total_amount, 0, '', ',') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-primary fw-bold">
                                    <td>Total</td>
                                    <td>{{ number_format($totalstock, 0, '', ',') }}</td>
                                    <td>Rs {{ number_format($totalamount, 0, '', ',') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

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

    <!-- Chambers Information Modal -->
    <div class="modal fade" id="chambersModal" tabindex="-1" aria-labelledby="chambersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="chambersModalLabel">
                        <i class="bi bi-layers-half me-2"></i>Chambers Information
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive mb-4">
                        <table class="table table-hover table-bordered border table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Chamber #</th>
                                    <th class="text-center">Capacity (ltr)</th>
                                    <th class="text-center">Dip</th>
                                    <th class="text-center">Rec. Dip</th>
                                    <th class="text-center">Gain/Loss</th>
                                    <th class="text-center">Ltr</th>
                                </tr>
                            </thead>
                            <tbody id="chamber_table_body">
                                <!-- Chamber data will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body" id="measurements_div">
                                    <!-- Measurements data will be loaded dynamically -->
                                </div>
                            </div>
                        </div>
                        <div id="message_div"
                            class="col-md-6 text-center d-flex align-items-center justify-content-center">
                            <!-- Any messages will be shown here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="printChambersBtn">
                        <i class="bi bi-printer me-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

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

        .form-control:focus,
        .form-select:focus {
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

        .btn-primary:hover,
        .btn-primary:focus {
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
    <script src="{{ asset('js/purchase-ajax.js') }}"></script>
@endpush
