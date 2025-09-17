@extends('admin.layout.master')

@section('title', 'Purchase History')
@section('description', 'Purchase History and Transaction Details')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/history.css') }}">
@endsection

@section('content')
@permission('history.purchases.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-cart-plus text-primary me-2"></i>Purchase History</h3>
            <p class="text-muted mb-0">Purchase records from {{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }}</p>
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
                    <form action="{{ route('admin.history.purchases') }}" method="get" class="row align-items-end">
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
                            <a href="{{ route('admin.history.purchases') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Details Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Purchase Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered history-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Purchase Date</th>
                                    <th class="text-center">Vendor</th>
                                    <th class="text-center">Product</th>
                                    <th class="text-center">Tank Lorry</th>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processedPurchases as $key => $purchase)
                                    <tr>
                                        <td>{{ $purchase->id }}</td>
                                        <td>{{ date('d-m-Y', strtotime($purchase->purchase_date)) }}</td>
                                        <td>
                                            {{ $purchase->vendor->vendor_name }}
                                            <span class="badge bg-secondary">{{ $purchase->vendor->vendor_type }}</span>
                                        </td>
                                        <td>
                                            {{ $purchase->product ? $purchase->product->name : 'Not found / deleted' }}
                                        </td>
                                        <td>
                                            {{ $purchase->tank_lorry ? $purchase->tank_lorry->larry_name : 'Not found' }}
                                        </td>
                                        <td>{{ number_format($purchase->previous_stock, 0, '', ',') }} <small>ltr</small></td>
                                        <td>{{ number_format($purchase->stock, 0, '', ',') }} <small>ltr</small></td>
                                        <td>{{ number_format($purchase->previous_stock + $purchase->stock, 0, '', ',') }} <small>ltr</small></td>
                                        <td><small>Rs</small> {{ number_format($purchase->rate, 2) }}</td>
                                        <td><small>Rs</small> {{ number_format($purchase->total_amount, 0, '', ',') }}</td>
                                        <td>
                                            {{ $purchase->driver ? $purchase->driver->driver_name : 'Not found' }}
                                        </td>
                                        <td>{{ number_format($purchase->sold_quantity, 0, '', ',') }}</td>
                                        <td>
                                            {{ $purchase->tank ? $purchase->tank->tank_name : 'Not found' }}
                                        </td>
                                        <td>
                                            @if($purchase->image_path)
                                                <a href="{{ $purchase->image_path }}" target="_blank" class="text-primary">View Receipt</a>
                                            @else
                                                <span class="text-muted">Not uploaded</span>
                                            @endif
                                        </td>
                                        <td>{{ $purchase->comments }}</td>
                                        <td><small>Rs</small> {{ number_format($purchase->rate_adjustment, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Total:</th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">{{ number_format($purchaseTotals->total_stock, 0, '', ',') }} <small>ltr</small></th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">-</th>
                                    <th class="text-center"><small>Rs</small> {{ number_format($purchaseTotals->total_amount, 0, '', ',') }}</th>
                                    <th colspan="6">-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Summary Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Purchase Summary</h5>
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
                                @foreach($purchaseStock as $stock)
                                    <tr>
                                        <td class="fw-medium">{{ $stock->product_name }}</td>
                                        <td class="text-center">{{ number_format($stock->total_quantity, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                                        <td class="text-center"><span class="fw-medium">Rs {{ number_format($stock->total_amount, 0, '', ',') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-center">{{ number_format($purchaseStockTotals->total_stock, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                                    <td class="text-center text-primary">Rs {{ number_format($purchaseStockTotals->total_amount, 0, '', ',') }}</td>
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
