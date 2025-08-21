@extends('admin.layout.master')

@section('title', 'Purchase Transport Report')
@section('description', 'Purchase Transport Analysis and Details')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/history.css') }}">
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-truck text-primary me-2"></i>Purchase Transport Report</h3>
            <p class="text-muted mb-0">Purchase transport analysis from {{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }}</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.purchase-transport') }}" method="get">
                        <!-- First Row of Filters -->
                        <div class="row align-items-end mb-3">
                            <div class="col-md-3 mb-3">
                                <label for="vendor_dropdown" class="form-label">Supplier</label>
                                <select name="vendor_dropdown" id="vendor_dropdown" class="form-select">
                                    <option value="">All Suppliers</option>
                                    @foreach(App\Models\Management\Suppliers::orderBy('name')->get() as $supplier)
                                        <option value="{{ $supplier->id }}"
                                                data-type="1"
                                                data-name="{{ $supplier->name }}"
                                                {{ ($vendorId == $supplier->id && $vendorType == '1') ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="product_filter" class="form-label">Product</label>
                                <select class="form-select" id="product_filter" name="product_filter">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="transport_id" class="form-label">Transport</label>
                                <select class="form-select" id="transport_id" name="transport_id">
                                    <option value="">All Transports</option>
                                    @foreach($lorries as $lorry)
                                        <option value="{{ $lorry->tid }}" {{ $transportId == $lorry->tid ? 'selected' : '' }}>
                                            {{ $lorry->larry_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                        </div>

                        <!-- Second Row of Filters -->
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('admin.reports.purchase-transport') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                                </a>
                            </div>
                            <div class="col-md-3 mb-3"></div>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $vendorId }}">
                        <input type="hidden" name="vendor_type" id="vendor_type" value="{{ $vendorType }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Transport Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Purchase Transport Details</h5>
                    <span class="badge bg-info">{{ count($purchases) }} Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover history-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Supplier</th>
                                    <th class="text-center">Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Rate</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Vehicle</th>
                                    <th class="text-center">Chambers</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalQuantity = 0;
                                    $totalAmount = 0;
                                @endphp

                                @foreach($purchases as $purchase)
                                    @php
                                        $totalQuantity += $purchase->stock;
                                        $totalAmount += $purchase->total_amount;

                                        // Get supplier name
                                        $supplier = null;
                                        if ($purchase->vendor_type == '1') {
                                            $supplier = App\Models\Management\Suppliers::find($purchase->supplier_id);
                                        }

                                        // Get product name
                                        $product = App\Models\Management\Product::find($purchase->product_id);

                                        // Get transport name
                                        $transport = App\Models\Management\TankLari::find($purchase->vehicle_no);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $purchase->id }}</td>
                                        <td class="text-center">{{ date('d-m-Y', strtotime($purchase->purchase_date)) }}</td>
                                        <td class="text-center">
                                            @if($supplier)
                                                <span class="badge bg-primary">{{ $supplier->name }}</span>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($product)
                                                {{ $product->name }}
                                            @else
                                                <span class="text-muted">Unknown Product</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ number_format($purchase->stock, 2) }}</td>
                                        <td class="text-center">Rs {{ number_format($purchase->rate, 2) }}</td>
                                        <td class="text-center">Rs {{ number_format($purchase->total_amount, 2) }}</td>
                                        <td class="text-center">
                                            @if($transport)
                                                <span class="badge bg-info">{{ $transport->larry_name }}</span>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    onclick="viewChambers({{ $purchase->id }})"
                                                    data-bs-toggle="tooltip"
                                                    title="View Chamber Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Totals:</th>
                                    <th class="text-center">{{ number_format($totalQuantity, 2) }}</th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">Rs {{ number_format($totalAmount, 2) }}</th>
                                    <th colspan="2">-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chamber Details Modal -->
<div class="modal fade" id="chamberModal" tabindex="-1" aria-labelledby="chamberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chamberModalLabel">
                    <i class="bi bi-info-circle me-2"></i>Chamber Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="chamberContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/reports.js') }}"></script>
<script>
    // Set the chamber data URL for the reports.js
    window.chamberDataUrl = "{{ route('admin.reports.chamber-data') }}";

    // Handle vendor dropdown selection for purchase transport
    $(document).ready(function() {
        $('#vendor_dropdown').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var vendorId = selectedOption.val();
            var vendorType = selectedOption.data('type');

            $('#vendor_id').val(vendorId);
            $('#vendor_type').val(vendorType);
        });
    });
</script>
@endsection
