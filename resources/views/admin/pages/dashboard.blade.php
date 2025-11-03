@extends('admin.layout.master')
@section('title', 'Stock Management | Admin Dashboard')
@section('description', 'Stock Management Admin Dashboard')
@section('content')
    @php
        $settings = App\Models\Management\Settings::first();
        $products = App\Models\Management\Product::with('tank')->get();
        // $nonDippableProducts = $products->where('is_dippable', 0);
        $recentLogs = App\Models\Logs::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        $totalOpeningStock = 0;
        $totalPurchasedStock = 0;
        $totalSoldStock = 0;
        $totalClosingStock = 0;
        $totalAvgSalePercent = 0;
        $totalCurrentStock = 0;

        $dippableProducts = $products->where('is_dippable', 1)->sortByDesc('id')->values()->all();

        // Data for charts
        $productNames = [];
        $productClosingStocks = [];

        foreach ($dippableProducts as $product) {
            $openingStock = App\Models\CurrentStock::where('product_id', $product->id)
                ->where('stock_date', '<', $settings->date_lock)
                ->orderByDesc('id')
                ->value('stock');

            $purchasedStock = App\Models\Purchase::where('product_id', $product->id)
                ->whereDate('purchase_date', $settings->date_lock)
                ->sum('stock');

            $soldStock = App\Models\Sales::where('product_id', $product->id)
                ->whereDate('create_date', $settings->date_lock)
                ->sum('quantity');

            $closedStock = App\Models\CurrentStock::where('product_id', $product->id)
                ->where('stock_date', $settings->date_lock)
                ->value('stock');

            $dateTime = new DateTime($settings->date_lock);
            $dateTime->setDate($dateTime->format('Y'), $dateTime->format('m'), 1);
            $firstDateOfMonth = $dateTime->format('Y-m-d');

            $totalAvgSalePer = App\Models\Sales::where('product_id', $product->id)
                ->where('create_date', '>=', $firstDateOfMonth)
                ->where('create_date', '<=', $settings->date_lock)
                ->sum('quantity');

            $tankStock = App\Models\Management\Tank::where('product_id', $product->id)->sum('opening_stock');

            $totalSold = $totalAvgSalePer;
            $startDate = new DateTime($settings->date_lock);
            $endDate = new DateTime($firstDateOfMonth);

            $interval = $startDate->diff($endDate);
            $daysInBetween = $interval->days + 1;
            $avg = $totalSold / $daysInBetween;

            $totalAvgSalePercent += $avg;
            $totalOpeningStock += $openingStock;
            $totalPurchasedStock += $purchasedStock;
            $totalSoldStock += $soldStock;
            $totalClosingStock += $closedStock;
            $totalCurrentStock += $tankStock;

            // Collect data for Products Stock chart (use closing stock on lock date)
            $productNames[] = $product->name;
            $productClosingStocks[] = (float) ($closedStock ?? 0);
        }

        // Monthly sales data (sum of quantity per month for the year of lock date)
        $year = \Carbon\Carbon::parse($settings->date_lock ?? now())->year;
        $monthlySales = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlySales[] = (float) \App\Models\Sales::whereYear('create_date', $year)
                ->whereMonth('create_date', $m)
                ->sum('quantity');
        }

        // Product stock data for charts
        // $productNames = $dippableProducts->pluck('name')->toArray();
        // $productStock = $dippableProducts->pluck('book_stock')->toArray();

    @endphp

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div>
                                <h2 class="fs-3 fw-bold mb-0">Dashboard</h2>
                                <p class="text-muted mb-0">Welcome to {{ $settings->company_name ?? 'Stock Management' }}
                                    Admin Panel</p>
                            </div>
                            <div class="ms-auto d-flex align-items-center">
                                <span class="text-muted me-2">System Locked At:</span>
                                <div class="badge bg-primary p-2 d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <span>{{ \Carbon\Carbon::parse($settings->date_lock ?? now())->format('F j, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Summary Cards - First Row -->
                        <div class="row g-3 mb-3">
                            <!-- Opening Stock -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="bi bi-box-arrow-in-right text-primary"
                                                    style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Opening Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">
                                                    {{ number_format($totalOpeningStock, 2) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Purchased Stock -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="bi bi-cart-plus text-success" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Purchased Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">
                                                    {{ number_format($totalPurchasedStock, 2) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sold Stock -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="bi bi-cart-dash text-danger" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Sold Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">
                                                    {{ number_format($totalSoldStock, 2) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Summary Cards - Second Row -->
                        <div class="row g-3">
                            <!-- Closing Stock -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="bi bi-box-arrow-right text-info" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Closing Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">
                                                    {{ number_format($totalClosingStock, 2) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Avg Sale % -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="bi bi-percent text-warning" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Avg Sale %</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">
                                                    {{ number_format($totalAvgSalePercent, 2) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Stock -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center"
                                                style="width: 60px; height: 60px;">
                                                <i class="bi bi-box-seam text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Current Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">
                                                    {{ number_format($totalCurrentStock, 2) }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dippable Products Stock Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0"><i class="bi bi-droplet text-success me-2"></i>Dippable Products Stock</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dippableProductsTable"
                                class="table table-hover table-bordered align-middle mb-0 w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Item Name</th>
                                        <th class="text-center">Opening stock</th>
                                        <th class="text-center">Purchased stock</th>
                                        <th class="text-center">Sold stock</th>
                                        <th class="text-center">Closing stock</th>
                                        <th class="text-center">Avg Sale %</th>
                                        <th class="text-center">Current stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dippableProducts as $index => $product)
                                        @php
                                            // $openingStock = App\Models\CurrentStock::where('product_id', $product->id)
                                            //     ->where('stock_date', '<', $settings->date_lock)
                                            //     ->orderByDesc('id')
                                            //     ->value('stock');

                                            $openingStock = 0;

                                            $latestSales = App\Models\Sales::where('product_id', $product->id)
                                                ->whereDate('create_date', '<', $settings->date_lock)
                                                ->orderByDesc('id')
                                                ->first();

                                            $latestPurchase = App\Models\Purchase::where('product_id', $product->id)
                                                ->whereDate('purchase_date','<', $settings->date_lock)
                                                ->orderByDesc('id')
                                                ->first();

                                            if ($latestSales && $latestPurchase) {
                                                if ($latestSales->created_at > $latestPurchase->created_at) {
                                                    $openingStock = $latestSales->previous_stock - $latestSales->quantity;
                                                } else {
                                                    $openingStock = $latestPurchase->previous_stock + $latestPurchase->stock;
                                                }
                                            } elseif ($latestSales) {
                                                $openingStock = $latestSales->previous_stock - $latestSales->quantity;
                                            } elseif ($latestPurchase) {
                                                $openingStock = $latestPurchase->previous_stock + $latestPurchase->stock;
                                            } else {
                                                $openingStock = 0;
                                            }


                                            $purchasedStock = App\Models\Purchase::where('product_id', $product->id)
                                                ->whereDate('purchase_date', $settings->date_lock)
                                                ->sum('stock');

                                            $soldStock = App\Models\Sales::where('product_id', $product->id)
                                                ->whereDate('create_date', $settings->date_lock)
                                                ->sum('quantity');

                                            // $closedStock = App\Models\CurrentStock::where('product_id', $product->id)
                                            //                     ->where('stock_date', $settings->date_lock)
                                            //                     ->value('stock');

                                            $closedStock = 0;

                                            $dateLock = new DateTime($settings->date_lock);

                                            $latestSales = App\Models\Sales::where('product_id', $product->id)
                                                ->whereDate('create_date', $settings->date_lock)
                                                ->orderByDesc('id')
                                                ->first();

                                            $latestPurchase = App\Models\Purchase::where('product_id', $product->id)
                                                ->whereDate('purchase_date', $settings->date_lock)
                                                ->orderByDesc('id')
                                                ->first();

                                            if ($latestSales && $latestPurchase) {
                                                if ($latestSales->created_at > $latestPurchase->created_at) {
                                                    $closedStock = $latestSales->previous_stock - $latestSales->quantity;
                                                } else {
                                                    $closedStock = $latestPurchase->previous_stock + $latestPurchase->stock;
                                                }
                                            } elseif ($latestSales) {
                                                $closedStock = $latestSales->previous_stock - $latestSales->quantity;
                                            } elseif ($latestPurchase) {
                                                $closedStock = $latestPurchase->previous_stock + $latestPurchase->stock;
                                            } else {
                                                $lastSales = App\Models\Sales::where('product_id', $product->id)
                                                    ->whereDate('create_date', '<', $settings->date_lock)
                                                    ->orderByDesc('create_date')
                                                    ->orderByDesc('id')
                                                    ->first();

                                                $lastPurchase = App\Models\Purchase::where('product_id', $product->id)
                                                    ->whereDate('purchase_date', '<', $settings->date_lock)
                                                    ->orderByDesc('purchase_date')
                                                    ->orderByDesc('id')
                                                    ->first();

                                                if ($lastSales && $lastPurchase) {
                                                    if ($lastSales->created_at > $lastPurchase->created_at) {
                                                        $closedStock = $lastSales->previous_stock - $lastSales->quantity;
                                                    } else {
                                                        $closedStock = $lastPurchase->previous_stock + $lastPurchase->stock;
                                                    }
                                                } elseif ($lastSales) {
                                                    $closedStock = $lastSales->previous_stock - $lastSales->quantity;
                                                } elseif ($lastPurchase) {
                                                    $closedStock = $lastPurchase->previous_stock + $lastPurchase->stock;
                                                } else {
                                                    $closedStock = 0;
                                                }
                                            }

                                            $dateTime = new DateTime($settings->date_lock);
                                            $dateTime->setDate($dateTime->format('Y'), $dateTime->format('m'), 1);
                                            $firstDateOfMonth = $dateTime->format('Y-m-d');

                                            $totalAvgSalePer = App\Models\Sales::where('product_id', $product->id)
                                                ->where('create_date', '>=', $firstDateOfMonth)
                                                ->where('create_date', '<=', $settings->date_lock)
                                                ->sum('quantity');

                                            $totalSold = $totalAvgSalePer;
                                            $startDate = new DateTime($settings->date_lock);
                                            $endDate = new DateTime($firstDateOfMonth);

                                            $interval = $startDate->diff($endDate);
                                            $daysInBetween = $interval->days + 1;
                                            $avg = $totalSold / $daysInBetween;

                                            $tankStock = App\Models\Management\Tank::where(
                                                'product_id',
                                                $product->id,
                                            )->sum('opening_stock');
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                        <span
                                                            class="text-primary">{{ substr($product->name, 0, 1) }}</span>
                                                    </div>
                                                    <span class="fw-medium">{{ $product->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ number_format($openingStock, 2) }} ltr</td>
                                            <td>{{ number_format($purchasedStock, 2) }} ltr</td>
                                            <td>{{ number_format($soldStock, 2) }} ltr</td>
                                            <td>{{ number_format($closedStock, 2) }} ltr</td>
                                            <td>{{ number_format($avg, 2) }}%</td>
                                            <td>{{ number_format($tankStock, 2) }} ltr</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Stock Charts -->
        <div class="row">
            <!-- Products Stock Distribution Chart -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-pie-chart text-primary me-2"></i>Products Stock</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="stockDistributionChart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales Trend Chart -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-graph-up text-success me-2"></i>Monthly Sales</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="monthlySalesChart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        @permission('logs.view')
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0"><i class="bi bi-activity text-primary me-2"></i>Recent Activity</h5>
                            <a href="{{ route('admin.logs.index') }}"
                                class="btn btn-sm btn-primary d-flex align-items-center">
                                <i class="bi bi-list-ul me-1"></i>
                                View All Logs
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="timeline p-4">
                                @foreach ($recentLogs as $log)
                                    <div class="timeline-item d-flex mb-3">
                                        <div class="timeline-marker flex-shrink-0 me-3">
                                            <div class="avatar-sm bg-{{ $log->action_type === 'Create' ? 'success' : ($log->action_type === 'Update' ? 'primary' : ($log->action_type === 'Delete' ? 'danger' : 'info')) }} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i
                                                    class="bi bi-{{ $log->action_type === 'Create' ? 'plus' : ($log->action_type === 'Update' ? 'pencil' : ($log->action_type === 'Delete' ? 'trash' : 'info-circle')) }} text-{{ $log->action_type === 'Create' ? 'success' : ($log->action_type === 'Update' ? 'primary' : ($log->action_type === 'Delete' ? 'danger' : 'info')) }}"></i>
                                            </div>
                                        </div>
                                        <div class="timeline-content flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                        style="width: 28px; height: 28px;">
                                                        <span class="text-primary fw-medium"
                                                            style="font-size: 0.875rem;">{{ substr($log->user->name, 0, 1) }}</span>
                                                    </div>
                                                    <span class="fw-medium">{{ $log->user->name }}</span>
                                                </div>
                                                <small class="text-muted ms-2">{{ $log->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0 text-gray-700">{{ $log->action_description }}</p>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($recentLogs->isEmpty())
                                    <div class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-activity text-muted opacity-50" style="font-size: 2.5rem;"></i>
                                            <p class="text-muted mt-2 mb-0">No recent activity found</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endpermission
    </div>

    <style>
        .timeline {
            position: relative;
        }

        .timeline-item {
            position: relative;
            padding-left: 2rem;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .timeline-content {
            margin-left: 3rem;
        }

        .avatar-sm {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }

        .border-light {
            border-color: #f8f9fa !important;
        }

        .badge-light {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Fixed styling issues */
        .avatar-sm {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        /* .container-fluid {
                max-width: 100%;
                overflow-x: hidden;
            } */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Ensure DataTables doesn't cause horizontal scroll */
        .dataTables_wrapper {
            width: 100%;
            overflow: hidden;
        }

        /* Improve chart visibility */
        canvas {
            max-width: 100%;
        }

        /* Additional styling for better visuals */
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

        /* DataTables specific styling */
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

        .timeline {
            position: relative;
            padding: 0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding-bottom: 15px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }

        .timeline-marker {
            height: 40px;
            width: 40px;
        }

        .empty-state {
            padding: 20px;
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.01);
        }

        .text-gray-700 {
            color: #495057;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            $('#dippableProductsTable').DataTable({
                responsive: false,
                scrollX: true,
                searching: true,
                ordering: true,
                paging: true,
                info: true,
                dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>t<"row mt-3"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
            });


            // Stock Distribution Chart
            const stockCtx = document.getElementById('stockDistributionChart').getContext('2d');
            new Chart(stockCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($productNames) !!},
                    datasets: [{
                        data: {!! json_encode($productClosingStocks) !!},
                        backgroundColor: ['#ffbb55', '#4154f1', '#ff771d', '#2eca6a'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                boxWidth: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.formattedValue;
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });

            // Monthly Sales Chart
            const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                    datasets: [{
                        label: 'Sales',
                        data: {!! json_encode($monthlySales) !!},
                        borderColor: '#4154f1',
                        backgroundColor: 'rgba(65, 84, 241, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
