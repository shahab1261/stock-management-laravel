@extends('admin.layout.master')

@section('title', 'Purchase Transport Report')
@section('description', 'Purchase Transport Analysis and Details')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/history.css') }}">
@endsection

@section('content')
@permission('reports.purchase-transport.view')
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
                        <!-- First Row of Filters (match Sale Transport UI) -->
                        <div class="row align-items-end mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="vendor_dropdown" class="form-label">Select Vendor</label>
                                <select name="vendor_dropdown" id="vendor_dropdown" class="form-select">
                                    <option value="">All Vendors</option>
                                    @foreach(App\Models\Management\Suppliers::orderBy('name')->get() as $supplier)
                                        <option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}" data-type="1" {{ ($vendorId == $supplier->id && $vendorType == '1') ? 'selected' : '' }}>
                                            {{ $supplier->name }} (Supplier)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Customers::orderBy('name')->get() as $customer)
                                        <option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-type="2" {{ ($vendorId == $customer->id && $vendorType == '2') ? 'selected' : '' }}>
                                            {{ $customer->name }} (Customer)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Product::orderBy('name')->get() as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-type="3" {{ ($vendorId == $product->id && $vendorType == '3') ? 'selected' : '' }}>
                                            {{ $product->name }} (Product)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Expenses::orderBy('expense_name')->get() as $expense)
                                        <option value="{{ $expense->eid }}" data-name="{{ $expense->expense_name }}" data-type="4" {{ ($vendorId == $expense->eid && $vendorType == '4') ? 'selected' : '' }}>
                                            {{ $expense->expense_name }} (Expense)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Incomes::orderBy('income_name')->get() as $income)
                                        <option value="{{ $income->id }}" data-name="{{ $income->income_name }}" data-type="5" {{ ($vendorId == $income->id && $vendorType == '5') ? 'selected' : '' }}>
                                            {{ $income->income_name }} (Income)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Banks::orderBy('name')->get() as $bank)
                                        <option value="{{ $bank->id }}" data-name="{{ $bank->name }}" data-type="6" {{ ($vendorId == $bank->id && $vendorType == '6') ? 'selected' : '' }}>
                                            {{ $bank->name }} (Bank)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\User::where('user_type', 3)->orderBy('name')->get() as $employee)
                                        <option value="{{ $employee->id }}" data-name="{{ $employee->name }}" data-type="9" {{ ($vendorId == $employee->id && $vendorType == '9') ? 'selected' : '' }}>
                                            {{ $employee->name }} (Employee)
                                        </option>
                                    @endforeach
                                    <option value="7" data-name="cash" data-type="7" {{ ($vendorId == '7' && $vendorType == '7') ? 'selected' : '' }}>Cash</option>
                                    <option value="8" data-name="mp" data-type="8" {{ ($vendorId == '8' && $vendorType == '8') ? 'selected' : '' }}>Mp</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="transport_id" class="form-label">Transport</label>
                                <select class="form-select" id="transport_id" name="transport_id">
                                    <option value="">All Transports</option>
                                    @foreach($lorries as $lorry)
                                        <option value="{{ $lorry->id ?? $lorry->tid }}" {{ ($transportId == ($lorry->id ?? $lorry->tid)) ? 'selected' : '' }}>
                                            {{ $lorry->larry_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="product_filter" class="form-label">Products</label>
                                <select class="form-select" id="product_filter" name="product_filter">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Second Row of Filters -->
                        <div class="row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>Submit
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('admin.reports.purchase-transport') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-x-circle me-2"></i>Clear
                                </a>
                            </div>
                        </div>

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
                    <span class="badge bg-primary">{{ count($purchases) }} Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table dataTable table-striped" style="width: 100%" id="table01">
                            <thead>
                                <tr>
                                    <th class="text-center">Purchase ID</th>
                                    <th class="text-center">Purchase Date</th>
                                    <th class="text-center">Vendor</th>
                                    <th class="text-center">Product</th>
                                    <th class="text-center">Tank Lorry</th>
                                    <th class="text-center">Purchased Stock</th>
                                    <th class="text-center">Driver</th>
                                    <th class="text-center">Invoice.Temp</th>
                                    <th class="text-center">Rec.Temp</th>
                                    <th class="text-center">Temp Loss/Gain</th>
                                    <th class="text-center">Dip Loss/Gain Ltr</th>
                                    <th class="text-center">Temp Loss/Gain Ltr</th>
                                    <th class="text-center">Actual Short Loss/Gain Ltr</th>
                                    <th class="text-center">Comments</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $stocks = 0;
                                    $templossgain = 0;
                                    $diplossgain = 0;
                                    $actuallossgain = 0;
                                @endphp
                                @foreach($purchases as $purchase)
                                    @php
                                        $stocks += ($purchase->stock ?? 0);
                                        $chamberDetails = \Illuminate\Support\Facades\DB::table('purchase_chambers_details')->where('purchase_id', $purchase->id)->first();
                                        $measurments = $chamberDetails?->measurements ? explode('_', $chamberDetails->measurements) : [];
                                        if (!empty($measurments)) {
                                            if (is_numeric($measurments[4] ?? null)) { $diplossgain += $measurments[4]; }
                                            if (is_numeric($measurments[5] ?? null)) { $templossgain += $measurments[5]; }
                                            if (is_numeric($measurments[6] ?? null)) { $actuallossgain += $measurments[6]; }
                                        } else { continue; }
                                        $vendor = app(\App\Http\Controllers\ReportsController::class)->getVendorByType((string)$purchase->vendor_type, (string)$purchase->supplier_id);
                                        $singleProduct = App\Models\Management\Product::find($purchase->product_id);
                                        $tankLari = App\Models\Management\TankLari::find($purchase->vehicle_no);
                                        $driver = App\Models\Management\Drivers::find($purchase->driver_no);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $purchase->id }}</td>
                                        <td class="text-center">{{ date('d-m-Y', strtotime($purchase->purchase_date)) }}</td>
                                        <td class="text-center">
                                            {{ $vendor->vendor_name ?? 'Unknown' }}
                                            <span class="badge bg-secondary">{{ $vendor->vendor_type ?? '' }}</span>
                                        </td>
                                        <td class="text-center">{{ $singleProduct?->name ?? 'Not found / deleted' }}</td>
                                        <td class="text-center">{{ $tankLari?->larry_name ?? 'Not found' }}</td>
                                        <td class="text-center">{{ number_format($purchase->stock) }} <small>ltr</small></td>
                                        <td class="text-center">{{ $driver?->driver_name ?? 'Not found' }}</td>
                                        <td class="text-center">{{ (!empty($measurments[1])) ? $measurments[1] : '-' }}</td>
                                        <td class="text-center">{{ (!empty($measurments[2])) ? $measurments[2] : '-' }}</td>
                                        <td class="text-center">{{ (!empty($measurments[3])) ? $measurments[3] : '-' }}</td>
                                        <td class="text-center">{{ (isset($measurments[4]) && $measurments[4] !== '') ? $measurments[4] : '-' }}</td>
                                        <td class="text-center">{{ (isset($measurments[5]) && $measurments[5] !== '') ? $measurments[5] : '-' }}</td>
                                        <td class="text-center">{{ (isset($measurments[6]) && $measurments[6] !== '') ? $measurments[6] : '-' }}</td>
                                        <td class="text-center">{{ $purchase->comments }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" onclick="viewChambers({{ $purchase->id }})" title="View Chambers" data-bs-toggle="modal" data-bs-target="#chamberModal"><i class="bi bi-truck"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td></td><td></td><td></td><td></td><td></td>
                                    <td>{{ number_format($stocks) }}</td>
                                    <td></td><td></td><td></td>
                                    <td></td>
                                    <td>{{ number_format($diplossgain) }}</td>
                                    <td>{{ number_format($templossgain) }}</td>
                                    <td>{{ number_format($actuallossgain) }}</td>
                                    <td></td><td></td>
                                </tr>
                            </tbody>
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
            <div class="modal-body" style="overflow-y:auto; max-height:70vh;">
                <div id="chamberContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endpermission
@endsection

@push('scripts')
<script>
    // Set the chamber data URL for the reports.js
    window.chamberDataUrl = "{{ route('admin.reports.chamber-data') }}";

    $(document).ready(function() {
        // Initialize DataTable like Sales Transport
        $('#table01').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 10,
            order: [[0, 'desc']],
        });

        // Handle vendor dropdown selection
        $('#vendor_dropdown').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var vendorType = selectedOption.data('type');
            $('#vendor_id').val(selectedOption.val());
            $('#vendor_type').val(vendorType);
        });
    });
</script>
@endpush
