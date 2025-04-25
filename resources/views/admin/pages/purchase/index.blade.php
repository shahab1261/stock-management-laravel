@extends('admin.layout.master')

@section('title', 'Manage Purchase')
@section('description', 'Manage System Purchase')

@section('content')

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
                    <button type="button" id="addNewPurchaseBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Purchase
                    </button>
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table id="purchaseTable" class="table table-hover align-middle mb-0" style="width:100%">
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
                                    {{-- <th class="text-center">Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchases as $key => $purchase)
                                <tr>
                                    <td class="ps-3 text-start">{{ $key + 1 }}</td>
                                    <td class="ps-3 text-start">
                                        {{ date('d-m-Y', strtotime($purchase->purchase_date)) }}
                                    </td>
                                    @php
                                        $vendor = $purchase->getVendorByType($purchase->vendor_type, $purchase->supplier_id);
                                        $productName = \App\Models\Management\Product::find($purchase->product_id)->name;
                                        $tankLorry = \App\Models\Management\TankLari::find($purchase->vehicle_no)->larry_name;
                                        $driverName = \App\Models\Management\Drivers::find($purchase->driver_no)->driver_name;
                                        $tankName = \App\Models\Management\Tank::find($purchase->tank_id)->tank_name;
                                    @endphp
                                    <td class="ps-3 text-start">{{ $vendor->vendor_type }}</td>
                                    <td class="ps-3 text-start">{{ $productName }}</td>
                                    <td class="ps-3 text-start">{{ $tankLorry ?? 'Not Found'}}</td>
                                    <td class="ps-3 text-start">{{ number_format($purchase->previous_stock, 0, '', ',') }} <small>ltr</small></td>
                                    <td class="ps-3 text-start">{{ number_format($purchase->stock, 0, '', ',') }} <small>ltr</small></td>
                                    <td class="ps-3 text-start">{{ number_format($purchase->previous_stock + $purchase->stock, 0, '', ',') }} <small>ltr</small></td>
                                    <td class="ps-3 text-start"><small>Rs</small> {{ $purchase->rate}}</td>
                                    <td class="ps-3 text-start"><small>Rs</small> {{ $purchase->total_amount }}</td>
                                    <td class="ps-3 text-start">{{ $driverName }}</td>
                                    <td class="ps-3 text-start">{{ $purchase->sold_quantity }}</td>
                                    <td class="ps-3 text-start">{{ $tankName }}</td>
                                    <td class="ps-3 text-start">
                                        @if($purchase->image_path)
                                        <a href="{{ $purchase->image_path }}" target="_blank">Receipt</a>
                                        @else
                                        <a href="#">Not Uploaded</a>
                                        @endif
                                    </td>
                                    <td class="ps-3 text-start">{{ $purchase->comments }}</td>
                                    <td class="ps-3 text-start">{{ $purchase->rate_adjustment }}</td>
                                    {{-- <td class="text-center ps-0">
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-terminal me-1"
                                            data-id="{{ $terminal->id }}"
                                            data-name="{{ $terminal->name }}"
                                            data-address="{{ $terminal->address }}"
                                            data-notes="{{ $terminal->notes }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-terminal" data-id="{{ $terminal->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td> --}}
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .avatar-sm {
        width: 36px;
        height: 36px;
        font-size: 16px;
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
        background-color: #3a4cd8;
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
</style>
@endsection

@push('scripts')
<script src="{{ asset('js/purchase-ajax.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#purchaseTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 10,
            order: [[0, 'asc']],
        });

        $('#addNewPurchaseBtn').click(function() {
            window.location.href = "{{ route('purchase.create') }}";
        });
    });
</script>
@endpush

