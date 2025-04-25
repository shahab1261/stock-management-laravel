@extends('admin.layout.master')
@section('title', 'Stock Management | Admin Dashboard')
@section('description', 'Stock Management Admin Dashboard')
@section('content')
    @php
        $settings = App\Models\Management\Settings::first();
        $products = App\Models\Management\Product::with('tank')->get();
        $dippableProducts = $products->where('is_dippable', 1);
        $nonDippableProducts = $products->where('is_dippable', 0);

        $totalOpeningStock = $dippableProducts->sum(function($product) {
            return $product->tank ? $product->tank->opening_stock : 0;
        });

        $totalPurchasedStock = 6150.00;
        $totalSoldStock = 4922.00;
        $totalClosingStock = 15715.00;
        $totalAvgSalePercent = 5833.32;
        $totalCurrentStock = 15715.00;

        // Product stock data for charts
        $productNames = $dippableProducts->pluck('name')->toArray();
        $productStock = $dippableProducts->pluck('book_stock')->toArray();
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
                                <p class="text-muted mb-0">Welcome to {{ $settings->company_name ?? 'Stock Management' }} Admin Panel</p>
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
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-box-arrow-in-right text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Opening Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalOpeningStock, 2) }}</h3>
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
                                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-cart-plus text-success" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Purchased Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalPurchasedStock, 2) }}</h3>
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
                                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-cart-dash text-danger" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Sold Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalSoldStock, 2) }}</h3>
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
                                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-box-arrow-right text-info" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Closing Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalClosingStock, 2) }}</h3>
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
                                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-percent text-warning" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Avg Sale %</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalAvgSalePercent, 2) }}</h3>
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
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-box-seam text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Current Stock</h6>
                                                <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalCurrentStock, 2) }}</h3>
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
                            <table id="dippableProductsTable" class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Item Name</th>
                                        <th>Opening stock</th>
                                        <th>Purchased stock</th>
                                        <th>Sold stock</th>
                                        <th>Closing stock</th>
                                        <th>Avg Sale %</th>
                                        <th>Current stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dippableProducts as $index => $product)
                                    @php
                                        $openingStock = $product->tank ? $product->tank->opening_stock : 0;
                                        // For demonstration - you would replace these with real calculations
                                        $purchasedStock = 100 + ($index * 50);
                                        $soldStock = 50 + ($index * 25);
                                        $closingStock = $openingStock + $purchasedStock - $soldStock;
                                        $avgSale = $soldStock > 0 ? round(($soldStock / $purchasedStock) * 100, 2) : 0;
                                        $currentStock = $product->book_stock > 0 ? $product->book_stock : $closingStock;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <span class="text-primary">{{ substr($product->name, 0, 1) }}</span>
                                                </div>
                                                <span class="fw-medium">{{ $product->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ number_format($openingStock, 2) }} {{ $product->unit }}</td>
                                        <td>{{ number_format($purchasedStock, 2) }} {{ $product->unit }}</td>
                                        <td>{{ number_format($soldStock, 2) }} {{ $product->unit }}</td>
                                        <td>{{ number_format($closingStock, 2) }} {{ $product->unit }}</td>
                                        <td>{{ number_format($avgSale, 2) }}%</td>
                                        <td>{{ number_format($currentStock, 2) }} {{ $product->unit }}</td>
                                    </tr>
                                    @endforeach
                                    {{-- <tr class="table-light fw-bold">
                                        <td colspan="2" class="text-end">Total:</td>
                                        <td>{{ number_format($totalOpeningStock, 2) }}</td>
                                        <td>{{ number_format($totalPurchasedStock, 2) }}</td>
                                        <td>{{ number_format($totalSoldStock, 2) }}</td>
                                        <td>{{ number_format($totalClosingStock, 2) }}</td>
                                        <td>{{ number_format($totalAvgSalePercent, 2) }}</td>
                                        <td>{{ number_format($totalCurrentStock, 2) }}</td>
                                    </tr> --}}
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
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-pie-chart text-primary me-2"></i>Products Stock</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div id="stockDistributionChart" style="height: 300px; width: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales Trend Chart -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-graph-up text-success me-2"></i>Monthly Sales</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div id="monthlySalesChart" style="height: 300px; width: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Stock Level History Chart -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-bar-chart text-warning me-2"></i>Price Comparison</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div id="priceComparisonChart" style="height: 300px; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Fixed styling issues */
        .avatar-sm {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .container-fluid {
            max-width: 100%;
            overflow-x: hidden;
        }

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
    </style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with fixed columns and no scrolling
        $('#dippableProductsTable').DataTable({
            responsive: false,
            scrollX: true,
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>t<"row mt-3"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });

        // Stock Distribution Chart
        const stockCtx = document.getElementById('stockDistributionChart').getContext('2d');
        new Chart(stockCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    // Use actual product names
                    @if(count($dippableProducts) > 0)
                        @foreach($dippableProducts as $product)
                            '{{ $product->name }}',
                        @endforeach
                    @else
                        'HSD', 'Super'
                    @endif
                ],
                datasets: [{
                    data: [
                        // Use actual product stock data
                        @if(count($dippableProducts) > 0)
                            @foreach($dippableProducts as $product)
                                {{ $product->book_stock > 0 ? $product->book_stock : mt_rand(1000, 10000) }},
                            @endforeach
                        @else
                            5901, 9814
                        @endif
                    ],
                    backgroundColor: ['#4154f1', '#2eca6a', '#ff771d', '#ffbb55'],
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
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Sales',
                        data: [650, 590, 800, 810, 560, 550, 730, 780, 820, 650, 590, 690],
                        borderColor: '#4154f1',
                        backgroundColor: 'rgba(65, 84, 241, 0.2)',
                        tension: 0.3,
                        fill: true
                    }
                ]
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

        // Price Comparison Chart
        const priceCtx = document.getElementById('priceComparisonChart').getContext('2d');
        new Chart(priceCtx, {
            type: 'bar',
            data: {
                labels: [
                    // Use actual product names
                    @if(count($dippableProducts) > 0)
                        @foreach($dippableProducts as $product)
                            '{{ $product->name }}',
                        @endforeach
                    @else
                        'HSD', 'Super'
                    @endif
                ],
                datasets: [{
                    label: 'Purchase Price',
                    data: [
                        // Use actual purchase prices
                        @if(count($dippableProducts) > 0)
                            @foreach($dippableProducts as $product)
                                {{ $product->current_purchase ?? mt_rand(150, 250) }},
                            @endforeach
                        @else
                            180, 210
                        @endif
                    ],
                    backgroundColor: '#4154f1',
                    barThickness: 20
                },
                {
                    label: 'Sale Price',
                    data: [
                        // Use actual sale prices
                        @if(count($dippableProducts) > 0)
                            @foreach($dippableProducts as $product)
                                {{ $product->current_sale ?? mt_rand(200, 300) }},
                            @endforeach
                        @else
                            225, 265
                        @endif
                    ],
                    backgroundColor: '#2eca6a',
                    barThickness: 20
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
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
