@extends('admin.layout.master')

@section('title', 'Sale Transport Report')
@section('description', 'Sale Transport Analysis and Details')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/history.css') }}">
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-truck-flatbed text-primary me-2"></i>Sale Transport Report</h3>
            <p class="text-muted mb-0">Sales transport analysis from {{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }}</p>
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
                    <form action="{{ route('admin.reports.sale-transport') }}" method="get">
                        <!-- First Row of Filters -->
                        <div class="row align-items-end mb-3">
                            <div class="col-md-3 mb-3">
                                <label for="vendor_dropdown" class="form-label">Customer</label>
                                <select name="vendor_dropdown" id="vendor_dropdown" class="form-select">
                                    <option value="">All Customers</option>
                                    @foreach(App\Models\Management\Customers::orderBy('name')->get() as $customer)
                                        <option value="{{ $customer->id }}"
                                                data-type="2"
                                                data-name="{{ $customer->name }}"
                                                {{ ($vendorId == $customer->id && $vendorType == '2') ? 'selected' : '' }}>
                                            {{ $customer->name }}
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
                                <a href="{{ route('admin.reports.sale-transport') }}" class="btn btn-secondary w-100">
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

    <!-- Sale Transport Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-truck-flatbed me-2"></i>Sale Transport Details</h5>
                    <span class="badge bg-info">{{ count($sales) }} Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover history-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Rate</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Vehicle</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalQuantity = 0;
                                    $totalAmount = 0;
                                @endphp

                                @foreach($sales as $sale)
                                    @php
                                        $totalQuantity += $sale->quantity;
                                        $totalAmount += $sale->amount;

                                        // Get customer name
                                        $customer = null;
                                        if ($sale->vendor_type == '2') {
                                            $customer = App\Models\Management\Customers::find($sale->customer_id);
                                        }

                                        // Get product name
                                        $product = App\Models\Management\Product::find($sale->product_id);

                                        // Get transport name
                                        $transport = App\Models\Management\TankLari::find($sale->tank_lari_id);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $sale->id }}</td>
                                        <td class="text-center">{{ date('d-m-Y', strtotime($sale->create_date)) }}</td>
                                        <td class="text-center">
                                            @if($customer)
                                                <span class="badge bg-success">{{ $customer->name }}</span>
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
                                        <td class="text-center">{{ number_format($sale->quantity, 2) }}</td>
                                        <td class="text-center">Rs {{ number_format($sale->rate, 2) }}</td>
                                        <td class="text-center">Rs {{ number_format($sale->amount, 2) }}</td>
                                        <td class="text-center">
                                            @if($transport)
                                                <span class="badge bg-info">{{ $transport->larry_name }}</span>
                                            @else
                                                <span class="text-muted">Unknown</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($sale->status ?? 1)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Completed
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock me-1"></i>Pending
                                                </span>
                                            @endif
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

    <!-- Transport Summary Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Transport Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Transport</th>
                                    <th class="text-center">Total Trips</th>
                                    <th class="text-center">Total Quantity</th>
                                    <th class="text-center">Total Amount</th>
                                    <th class="text-center">Average per Trip</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $transportSummary = collect($sales)->groupBy('tank_lari_id')->map(function($transportSales, $transportId) {
                                        $transport = App\Models\Management\TankLari::find($transportId);
                                        return [
                                            'name' => $transport ? $transport->larry_name : 'Unknown',
                                            'trips' => $transportSales->count(),
                                            'quantity' => $transportSales->sum('quantity'),
                                            'amount' => $transportSales->sum('amount'),
                                            'average' => $transportSales->count() > 0 ? $transportSales->sum('amount') / $transportSales->count() : 0
                                        ];
                                    })->sortByDesc('amount');
                                @endphp

                                @foreach($transportSummary as $summary)
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $summary['name'] }}</span>
                                        </td>
                                        <td class="text-center">{{ $summary['trips'] }}</td>
                                        <td class="text-center">{{ number_format($summary['quantity'], 2) }}</td>
                                        <td class="text-center">Rs {{ number_format($summary['amount'], 2) }}</td>
                                        <td class="text-center">Rs {{ number_format($summary['average'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ count($sales) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart-check fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Quantity
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalQuantity, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Amount
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs {{ number_format($totalAmount, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg per Sale
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs {{ count($sales) > 0 ? number_format($totalAmount / count($sales), 2) : '0.00' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calculator fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('assets/js/reports.js') }}"></script>
<script>
    // Handle vendor dropdown selection for sale transport
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
