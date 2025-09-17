@extends('admin.layout.master')

@section('title', 'Sales History')
@section('description', 'Sales History and Transaction Details')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/history.css') }}">
@endsection

@section('content')
@permission('history.sales.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-cart-check text-primary me-2"></i>Sales History</h3>
            <p class="text-muted mb-0">Sales records from {{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }}</p>
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
                    <form action="{{ route('admin.history.sales') }}" method="get" class="row align-items-end">
                        <div class="mb-3" style="width: 202px;">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="mb-3" style="width: 202px;">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="mb-3" style="width: 202px;">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select searchable-dropdown" id="product_id" name="product_id">
                                <option selected disabled>All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('admin.history.sales') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Details Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-cart-check me-2"></i>Sales Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered history-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Sales Date</th>
                                    <th class="text-center">Vendor</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Tank Lorry</th>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processedSales as $key => $sale)
                                    <tr>
                                        <td>{{ $sale->id }}</td>
                                        <td>{{ date('d-m-Y', strtotime($sale->create_date)) }}</td>
                                        <td>
                                            {{ $sale->vendor->vendor_name }}
                                            <span class="badge bg-secondary">{{ $sale->vendor->vendor_type }}</span>
                                        </td>
                                        <td>
                                            {{ $sale->product ? $sale->product->name : 'Not found / deleted' }}
                                        </td>
                                        <td>
                                            {{ $sale->tank_lorry ? $sale->tank_lorry->larry_name : 'No tank lorry found' }}
                                        </td>
                                        <td>{{ number_format($sale->previous_stock, 0, '', ',') }} <small>ltr</small></td>
                                        <td>{{ number_format($sale->quantity, 0, '', ',') }} <small>ltr</small></td>
                                        <td>{{ number_format($sale->previous_stock - $sale->quantity, 0, '', ',') }} <small>ltr</small></td>
                                        <td>Rs {{ number_format($sale->rate, 2) }}</td>
                                        <td>Rs {{ number_format($sale->amount, 0, '', ',') }}</td>
                                        <td>
                                            @if($sale->freight == 1)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>Rs {{ number_format($sale->freight_charges, 0, '', ',') }}</td>
                                        <td>
                                            {{ $sale->tank ? $sale->tank->tank_name : 'Not found' }}
                                        </td>
                                        <td>
                                            @if($sale->sales_type == 1)
                                                <span class="badge bg-primary">Cash</span>
                                            @elseif($sale->sales_type == 2)
                                                <span class="badge bg-warning text-dark">Credit</span>
                                            @else
                                                <span class="badge bg-secondary">Other</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sale->profit > 0)
                                                <span class="text-success">+Rs {{ number_format($sale->profit, 0, '', ',') }}</span>
                                            @elseif($sale->profit < 0)
                                                <span class="text-danger">Rs {{ number_format($sale->profit, 0, '', ',') }}</span>
                                            @else
                                                <span class="text-muted">Rs 0</span>
                                            @endif
                                        </td>
                                        <td>{{ $sale->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Total:</th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">{{ number_format($salesTotals->total_quantity, 0, '', ',') }} <small>ltr</small></th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">-</th>
                                    <th class="text-center"><small>Rs</small> {{ number_format($salesTotals->total_amount, 0, '', ',') }}</th>
                                    <th colspan="6">-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Summary Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Sales Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesSummary as $summary)
                                    <tr>
                                        <td class="fw-medium">{{ $summary->product_name }}</td>
                                        <td class="text-center">{{ number_format($summary->total_quantity, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                                        <td class="text-center"><span class="fw-medium">Rs {{ number_format($summary->total_amount, 0, '', ',') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-center">{{ number_format($salesSummaryTotals->total_quantity, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                                    <td class="text-center text-primary">Rs {{ number_format($salesSummaryTotals->total_amount, 0, '', ',') }}</td>
                                </tr>
                            </tfoot>
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
<script src="{{ asset('js/history-ajax.js') }}"></script>
@endpush
