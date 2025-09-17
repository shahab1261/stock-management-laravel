@extends('admin.layout.master')

@section('title', 'Sale Transport Report')
@section('description', 'Sale Transport Analysis and Details')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/history.css') }}">
@endsection

@section('content')
@permission('reports.sale-transport.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-truck-flatbed text-primary me-2"></i>Sale Transport Report</h3>
            <p class="text-muted mb-0">Sales transport analysis from {{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3"
                        style="width: 66px; height: 66px;">
                        <i class="bi bi-cart-check text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Sales</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($sales->count()) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3"
                        style="width: 66px; height: 66px;">
                        <i class="bi bi-layers text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Quantity</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($sales->sum('quantity')) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-info bg-opacity-10 p-3 me-3"
                        style="width: 66px; height: 66px;">
                        <i class="bi bi-currency-dollar text-info" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Amount</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($sales->sum('amount')) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3"
                        style="width: 66px; height: 66px;">
                        <i class="bi bi-truck text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Freight Charges</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($sales->sum('freight_charges')) }}</h3>
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
                    <form action="{{ route('admin.reports.sale-transport') }}" method="post">
                        @csrf
                        <!-- First Row of Filters (3 fields) -->
                        <div class="row align-items-end mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="vendor_id" class="form-label">Select Vendor</label>
                                <select name="vendor_id" id="vendor_id" class="form-select searchable-dropdown">
                                    <option selected disabled>All Vendors</option>
                                    @foreach(App\Models\Management\Suppliers::orderBy('name')->get() as $supplier)
                                        <option value="{{ $supplier->id }}"
                                                data-name="{{ $supplier->name }}"
                                                data-type="1"
                                                {{ ($vendorId == $supplier->id && $vendorType == '1') ? 'selected' : '' }}>
                                            {{ $supplier->name }} (Supplier)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Customers::orderBy('name')->get() as $customer)
                                        <option value="{{ $customer->id }}"
                                                data-name="{{ $customer->name }}"
                                                data-type="2"
                                                {{ ($vendorId == $customer->id && $vendorType == '2') ? 'selected' : '' }}>
                                            {{ $customer->name }} (Customer)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Product::orderBy('name')->get() as $product)
                                        <option value="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-type="3"
                                                {{ ($vendorId == $product->id && $vendorType == '3') ? 'selected' : '' }}>
                                            {{ $product->name }} (Product)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Expenses::orderBy('expense_name')->get() as $expense)
                                        <option value="{{ $expense->eid }}"
                                                data-name="{{ $expense->expense_name }}"
                                                data-type="4"
                                                {{ ($vendorId == $expense->eid && $vendorType == '4') ? 'selected' : '' }}>
                                            {{ $expense->expense_name }} (Expense)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Incomes::orderBy('income_name')->get() as $income)
                                        <option value="{{ $income->id }}"
                                                data-name="{{ $income->income_name }}"
                                                data-type="5"
                                                {{ ($vendorId == $income->id && $vendorType == '5') ? 'selected' : '' }}>
                                            {{ $income->income_name }} (Income)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\Management\Banks::orderBy('name')->get() as $bank)
                                        <option value="{{ $bank->id }}"
                                                data-name="{{ $bank->name }}"
                                                data-type="6"
                                                {{ ($vendorId == $bank->id && $vendorType == '6') ? 'selected' : '' }}>
                                            {{ $bank->name }} (Bank)
                                        </option>
                                    @endforeach
                                    @foreach(App\Models\User::role('Employee')->orderBy('name')->get() as $employee)
                                        <option value="{{ $employee->id }}"
                                                data-name="{{ $employee->name }}"
                                                data-type="9"
                                                {{ ($vendorId == $employee->id && $vendorType == '9') ? 'selected' : '' }}>
                                            {{ $employee->name }} (Employee)
                                        </option>
                                    @endforeach
                                    <option value="7" data-name="cash" data-type="7" {{ ($vendorId == '7' && $vendorType == '7') ? 'selected' : '' }}>Cash</option>
                                    <option value="8" data-name="mp" data-type="8" {{ ($vendorId == '8' && $vendorType == '8') ? 'selected' : '' }}>Mp</option>
                                </select>
                                <input type="hidden" id="vendor_type" name="vendor_type" value="{{ $vendorType }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="transport_id" class="form-label">Transport</label>
                                <select class="form-select searchable-dropdown" id="transport_id" name="transport_id">
                                    <option selected disabled>All Transports</option>
                                    @foreach($lorries as $lorry)
                                        <option value="{{ $lorry->id }}" {{ $transportId == $lorry->id ? 'selected' : '' }}>
                                            {{ $lorry->larry_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="product_filter" class="form-label">Products</label>
                                <select class="form-select searchable-dropdown" id="product_filter" name="product_filter">
                                    <option selected disabled>All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Second Row of Filters (4 fields) -->
                        <div class="row align-items-end mb-3">
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
                                <a href="{{ route('admin.reports.sale-transport') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-x-circle me-2"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Transport Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-truck-flatbed me-2"></i>Sale Transport Details</h5>
                    <span class="badge bg-primary">{{ count($sales) }} Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table dataTable table-striped" style="width: 100%" id="table01">
                            <thead>
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Sale Date</th>
                                    <th>Vendor</th>
                                    <th>Product</th>
                                    <th>Tank Lorry</th>
                                    <th>Sold Stock</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th>Freight Charges</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $stocks = 0;
                                    $amounts = 0;
                                    $freight_sum = 0;
                                @endphp

                                @foreach($sales as $key => $sale)
                                    @php
                                        $stocks += $sale->quantity;
                                        $amounts += $sale->amount;
                                        $freight_sum += $sale->freight_charges;

                                        // Get vendor details
                                        $vendor = null;
                                        if ($sale->vendor_type == '1') {
                                            $vendor = App\Models\Management\Suppliers::find($sale->customer_id);
                                            $vendorType = 'Supplier';
                                        } elseif ($sale->vendor_type == '2') {
                                            $vendor = App\Models\Management\Customers::find($sale->customer_id);
                                            $vendorType = 'Customer';
                                        } elseif ($sale->vendor_type == '3') {
                                            $vendor = App\Models\Management\Product::find($sale->customer_id);
                                            $vendorType = 'Product';
                                        } elseif ($sale->vendor_type == '4') {
                                            $vendor = App\Models\Management\Expenses::find($sale->customer_id);
                                            $vendorType = 'Expense';
                                        } elseif ($sale->vendor_type == '5') {
                                            $vendor = App\Models\Management\Incomes::find($sale->customer_id);
                                            $vendorType = 'Income';
                                        } elseif ($sale->vendor_type == '6') {
                                            $vendor = App\Models\Management\Banks::find($sale->customer_id);
                                            $vendorType = 'Bank';
                                        } elseif ($sale->vendor_type == '7') {
                                            $vendor = (object)['name' => 'Cash'];
                                            $vendorType = 'Cash';
                                        } elseif ($sale->vendor_type == '8') {
                                            $vendor = (object)['name' => 'MP'];
                                            $vendorType = 'MP';
                                        } elseif ($sale->vendor_type == '9') {
                                            $vendor = App\Models\User::find($sale->customer_id);
                                            $vendorType = 'Employee';
                                        }

                                        // Get product details
                                        $product = App\Models\Management\Product::find($sale->product_id);

                                        // Get tank lorry details
                                        $tankLari = App\Models\Management\TankLari::find($sale->tank_lari_id);
                                    @endphp
                                    <tr>
                                        <td>{{ $sale->id }}</td>
                                        <td>{{ date('d-m-Y', strtotime($sale->create_date)) }}</td>
                                        <td>
                                            @if($vendor)
                                                {{ $vendor->name ?? $vendor->expense_name ?? $vendor->income_name ?? 'Unknown' }}
                                                <span class="badge bg-secondary">{{ $vendorType }}</span>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product)
                                                {{ $product->name }}
                                            @else
                                                <span class="text-muted">Not found / deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tankLari)
                                                {{ $tankLari->larry_name }}
                                            @else
                                                <span class="text-muted">Not found</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($sale->quantity) }} <small>ltr</small></td>
                                        <td>{{ number_format($sale->rate) }}</td>
                                        <td>{{ number_format($sale->amount) }}</td>
                                        <td>{{ number_format($sale->freight_charges) }}</td>
                                        <td>{{ $sale->notes }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ number_format($stocks) }}</td>
                                    <td></td>
                                    <td>{{ number_format($amounts) }}</td>
                                    <td>{{ number_format($freight_sum) }}</td>
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
@endpermission
@endsection

@push('scripts')
<script src="{{ asset('assets/js/reports.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
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
        $('#vendor_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var vendorType = selectedOption.data('type');
            $('#vendor_type').val(vendorType);
            console.log('Vendor selected:', selectedOption.val(), 'Type:', vendorType);
        });

        // Remove auto-submit to prevent logout issues
        // $('#vendor_id').on('change', function() {
        //     if ($(this).val()) {
        //         setTimeout(function() {
        //             $('form').first().submit();
        //         }, 500);
        //     }
        // });

        // Set vendor type on page load if vendor is already selected
        $(document).ready(function() {
            var selectedVendor = $('#vendor_id option:selected');
            if (selectedVendor.val()) {
                var vendorType = selectedVendor.data('type');
                $('#vendor_type').val(vendorType);
            }
        });

        // Debug form submission
        $('form').on('submit', function(e) {
            console.log('Form submitting with data:', {
                vendor_id: $('#vendor_id').val(),
                vendor_type: $('#vendor_type').val(),
                product_filter: $('#product_filter').val(),
                transport_id: $('#transport_id').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val()
            });
        });

        // Set document title
        document.title = "Sale Transport Report";
    });
</script>
@endpush
