@extends('admin.layout.master')

@section('title', 'Billing Report')
@section('description', 'Credit Sale Transport Report')

@push('styles')
    <link href="{{ asset('css/billing.css') }}" rel="stylesheet">
@endpush

@section('content')
    @permission('billing.view')
        <div class="container-fluid py-4">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h3 class="mb-0"><i class="bi bi-receipt text-primary me-2"></i>Credit Sale Transport Report</h3>
                    <p class="text-muted mb-0">Analyze credit sales by transport from {{ date('d M Y', strtotime($startDate)) }}
                        to {{ date('d M Y', strtotime($endDate)) }}</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3"
                                style="width: 66px; height: 66px;">
                                <i class="bi bi-list-ol text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Records</h6>
                                <h3 class="mb-0" style="font-size: 1.7rem;">{{ $sales->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="d-flex justify-content-center align-items-center rounded-circle bg-info bg-opacity-10 p-3 me-3"
                                style="width: 66px; height: 66px;">
                                <i class="bi bi-droplet text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Stock</h6>
                                <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalStock, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3"
                                style="width: 66px; height: 66px;">
                                <i class="bi bi-currency-rupee text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Amount</h6>
                                <h3 class="mb-0 text-success" style="font-size: 1.7rem;">Rs {{ number_format($totalAmount, 2) }}
                                </h3>
                            </div>
                        </div>
                    </div>
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
                            <form method="GET" action="{{ route('admin.billing.index') }}" class="row align-items-end">
                                <div class="col-md-2 mb-3">
                                    <label for="vendor_id" class="form-label">Select Vendor</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <select name="vendor_id" id="vendor_id" class="form-select border-start-0 searchable-dropdown">
                                            <option selected disabled>All Vendors</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}"
                                                    data-type="1"
                                                    {{ $vendorId == $supplier->id && $vendorType == '1' ? 'selected' : '' }}>
                                                    {{ $supplier->name }} (Supplier)
                                                </option>
                                            @endforeach
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}" data-name="{{ $customer->name }}"
                                                    data-type="2"
                                                    {{ $vendorId == $customer->id && $vendorType == '2' ? 'selected' : '' }}>
                                                    {{ $customer->name }} (Customer)
                                                </option>
                                            @endforeach
                                            @foreach ($allProducts as $product)
                                                <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                                    data-type="3"
                                                    {{ $vendorId == $product->id && $vendorType == '3' ? 'selected' : '' }}>
                                                    {{ $product->name }} (Product)
                                                </option>
                                            @endforeach
                                            @foreach ($expenses as $expense)
                                                <option value="{{ $expense->id }}" data-name="{{ $expense->expense_name }}"
                                                    data-type="4"
                                                    {{ $vendorId == $expense->id && $vendorType == '4' ? 'selected' : '' }}>
                                                    {{ $expense->expense_name }} (Expense)
                                                </option>
                                            @endforeach
                                            @foreach ($incomes as $income)
                                                <option value="{{ $income->id }}" data-name="{{ $income->income_name }}"
                                                    data-type="5"
                                                    {{ $vendorId == $income->id && $vendorType == '5' ? 'selected' : '' }}>
                                                    {{ $income->income_name }} (Income)
                                                </option>
                                            @endforeach
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}" data-name="{{ $bank->name }}"
                                                    data-type="6"
                                                    {{ $vendorId == $bank->id && $vendorType == '6' ? 'selected' : '' }}>
                                                    {{ $bank->name }} (Bank)
                                                </option>
                                            @endforeach
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                                    data-type="9"
                                                    {{ $vendorId == $employee->id && $vendorType == '9' ? 'selected' : '' }}>
                                                    {{ $employee->name }} (Employee)
                                                </option>
                                            @endforeach
                                            <option value="7" data-name="cash" data-type="7"
                                                {{ $vendorId == '7' && $vendorType == '7' ? 'selected' : '' }}>Cash</option>
                                            <option value="8" data-name="mp" data-type="8"
                                                {{ $vendorId == '8' && $vendorType == '8' ? 'selected' : '' }}>Mp</option>
                                        </select>
                                    </div>
                                    <input type="hidden" id="vendor_type" name="vendor_type" value="{{ $vendorType }}">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="transport_id" class="form-label">Transport</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-truck"></i>
                                        </span>
                                        <select name="transport_id" id="transport_id" class="form-select border-start-0 searchable-dropdown">
                                            <option selected disabled>All Transports</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}"
                                                    {{ $transportId == $vehicle->id ? 'selected' : '' }}>
                                                    {{ $vehicle->larry_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="product_filter" class="form-label">Products</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-box-seam"></i>
                                        </span>
                                        <select name="product_filter" id="product_filter" class="form-select border-start-0 searchable-dropdown">
                                            <option selected disabled>All Products</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ $productId == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-calendar"></i>
                                        </span>
                                        <input type="date" class="form-control border-start-0" name="start_date"
                                            id="start_date" value="{{ $startDate }}">
                                    </div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-calendar"></i>
                                        </span>
                                        <input type="date" class="form-control border-start-0" name="end_date"
                                            id="end_date" value="{{ $endDate }}">
                                    </div>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search me-2"></i>Filter
                                    </button>
                                </div>
                                <div class="col-md-1 mb-3">
                                    <a href="{{ route('admin.billing.index') }}" class="btn btn-secondary w-100"
                                        style="padding: 11px;">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Summary</h5>
                            <div class="d-flex gap-2">
                                @permission('billing.export')
                                    <button type="button" id="exportBtn" class="btn btn-success btn-sm">
                                        <i class="bi bi-download me-1"></i>Export
                                    </button>
                                @endpermission
                                <button type="button" id="printBtn" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-printer me-1"></i>Print
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle mb-0" id="summaryTable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="ps-3 text-center">#</th>
                                            <th class="ps-3">Vendor</th>
                                            <th class="ps-3">Vehicle No.</th>
                                            <th class="ps-3 text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $counter = 1; @endphp
                                        @foreach ($salesGroupBy as $sale)
                                            <tr>
                                                <td class="text-center">{{ $counter++ }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php
                                                            $creditSalesModel = new App\Models\CreditSales();
                                                            $vendor = $creditSalesModel->getVendorByType(
                                                                $sale->vendor_type,
                                                                $sale->vendor_id,
                                                            );
                                                        @endphp
                                                        <div
                                                            class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            <span
                                                                class="text-primary">{{ substr($vendor->vendor_name, 0, 1) }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="fw-medium">{{ $vendor->vendor_name }}</span>
                                                            <small class="text-muted d-block">
                                                                <span
                                                                    class="badge bg-secondary">{{ ucfirst($vendor->vendor_type) }}</span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $vehicle = App\Models\Management\TankLari::find(
                                                            $sale->vehicle_id,
                                                        );
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="avatar-sm bg-info bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-truck text-info"></i>
                                                        </div>
                                                        <span
                                                            class="fw-medium">{{ $vehicle ? $vehicle->larry_name : 'Not found' }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">Rs {{ number_format($sale->amountsum, 2) }}</td>
                                            </tr>
                                        @endforeach

                                        <!-- Total Row -->
                                        <tr class="table-primary">
                                            <td class="text-center fw-bold">{{ $counter }}</td>
                                            <td class="fw-bold">Total</td>
                                            <td></td>
                                            <td class="text-end fw-bold">Rs {{ number_format($summaryTotalAmount, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Details</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle mb-0" id="detailsTable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="ps-3 text-center">Date</th>
                                            <th class="ps-3">Vendor</th>
                                            <th class="ps-3">Product</th>
                                            <th class="ps-3">Tank Lorry</th>
                                            <th class="ps-3 text-end">Sold Stock</th>
                                            <th class="ps-3 text-end">Rate</th>
                                            <th class="ps-3 text-end">Amount</th>
                                            <th class="ps-3">Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sales as $sale)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $sale->transasction_date ? $sale->transasction_date->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php
                                                            $creditSalesModel = new App\Models\CreditSales();
                                                            $vendor = $creditSalesModel->getVendorByType(
                                                                $sale->vendor_type,
                                                                $sale->vendor_id,
                                                            );
                                                        @endphp
                                                        <div
                                                            class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            <span
                                                                class="text-primary">{{ substr($vendor->vendor_name, 0, 1) }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="fw-medium">{{ $vendor->vendor_name }}</span>
                                                            <small class="text-muted d-block">
                                                                <span
                                                                    class="badge bg-secondary">{{ ucfirst($vendor->vendor_type) }}</span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="avatar-sm bg-success bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            <span
                                                                class="text-success">{{ $sale->product ? substr($sale->product->name, 0, 1) : 'N' }}</span>
                                                        </div>
                                                        <span
                                                            class="fw-medium">{{ $sale->product ? $sale->product->name : 'Not found / deleted' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="avatar-sm bg-info bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-truck text-info"></i>
                                                        </div>
                                                        <span
                                                            class="fw-medium">{{ $sale->vehicle ? $sale->vehicle->larry_name : 'Not found' }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ number_format($sale->quantity, 2) }} <small
                                                        class="text-muted">ltr</small></td>
                                                <td class="text-end">{{ number_format($sale->rate, 2) }}</td>
                                                <td class="text-end">Rs {{ number_format($sale->amount, 2) }}</td>
                                                <td>{{ $sale->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach

                                        <!-- Total Row -->
                                        <tr class="table-light">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end fw-bold">{{ number_format($totalStock, 2) }} ltr</td>
                                            <td></td>
                                            <td class="text-end fw-bold">Rs {{ number_format($totalAmount, 2) }}</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden data for JavaScript -->
        <script type="text/javascript">
            window.billingData = @json($sales);
            window.routes = {
                export: '{{ route('admin.billing.export') }}'
            };
            window.filterParams = {
                vendor_id: '{{ $vendorId }}',
                vendor_type: '{{ $vendorType }}',
                product_filter: '{{ $productId }}',
                start_date: '{{ $startDate }}',
                end_date: '{{ $endDate }}',
                transport_id: '{{ $transportId }}'
            };
        </script>
    @endpermission
@endsection

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

    .form-control:focus,
    .form-select:focus {
        border-color: #4154f1;
        box-shadow: 0 0 0 0.25rem rgba(65, 84, 241, 0.1);
    }

    .input-group-text {
        color: #6c757d;
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

    .table-warning {
        background-color: rgba(255, 193, 7, 0.1);
        border-left: 4px solid #ffc107;
    }
</style>

@push('scripts')
    <script src="{{ asset('js/billing.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#summaryTable').DataTable({
                processing: true,
                responsive: false,
                scrollX: true,
                dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [25, 50, 100, -1],
                    [25, 50, 100, "All"]
                ],
                pageLength: 25,
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                        targets: [3],
                        className: 'text-end'
                    },
                    {
                        targets: 0,
                        type: 'num'
                    }
                ]
            });

            $('#detailsTable').DataTable({
                processing: true,
                responsive: false,
                scrollX: true,
                dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [25, 50, 100, -1],
                    [25, 50, 100, "All"]
                ],
                pageLength: 50,
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                        targets: [4, 5, 6],
                        className: 'text-end'
                    },
                    {
                        targets: [0],
                        className: 'text-center'
                    }
                ]
            });

            document.title = "Credit Sale Transport Report";
        });
    </script>
@endpush
