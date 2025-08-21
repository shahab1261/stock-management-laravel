@extends('admin.layout.master')

@section('title', 'All Stocks Report')
@section('description', 'Complete Stock Analysis and Status')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/history.css') }}">
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-box-seam text-primary me-2"></i>All Stocks Report</h3>
            <p class="text-muted mb-0">Complete stock analysis and status for all products</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-box-seam text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Products</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ count($productData) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-arrow-down-circle text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Purchase</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($purchasedTotal) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-info bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-arrow-up-circle text-info" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Sales</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($soldTotal) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-warning bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-database text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Current Stock</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($currentTotal) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Stocks Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>All Stocks Report</h5>
                    @if($dateLock)
                        <span class="badge bg-primary">
                            <i class="bi bi-lock me-1"></i>System Locked at: {{ date('d-m-Y', strtotime($dateLock)) }}
                        </span>
                    @endif
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="allStocksTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="ps-3 text-center">Item Name</th>
                                    <th class="ps-3 text-center">Opening <small>stock</small></th>
                                    <th class="ps-3 text-center">Purchased <small>stock</small></th>
                                    <th class="ps-3 text-center">Sold <small>stock</small></th>
                                    <th class="ps-3 text-center">Closing <small>stock</small></th>
                                    <th class="ps-3 text-center">Avg Sale % <small>stock</small></th>
                                    <th class="ps-3 text-center">Current <small>stock</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productData as $data)
                                    <tr>
                                        <td class="text-center">{{ $data['product']->name }}</td>
                                        <td class="text-end">
                                            @if($data['opening_stock'] > 0)
                                                {{ number_format($data['opening_stock']) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($data['purchased_stock']) }}</td>
                                        <td class="text-end">{{ number_format($data['sold_stock']) }}</td>
                                        <td class="text-end">
                                            @if($data['closing_stock'] > 0)
                                                {{ number_format($data['closing_stock']) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($data['avg_sale'] > 0)
                                                {{ number_format($data['avg_sale'], 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($data['current_stock']) }}</td>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $("#allStocksTable").DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            pageLength: 25,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [1,2,3,4,5,6], className: 'text-end' },
                { targets: [0], className: 'text-center' }
            ]
        });
        document.title = "All Stocks Report";
    });
</script>
@endpush

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
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn-danger:hover, .btn-danger:focus {
        background-color: #c82333;
        border-color: #bd2130;
    }
    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }
    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
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
</style>
