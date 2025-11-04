@extends('admin.layout.master')

@section('title', 'Wet Stock Analysis')
@section('description', 'Analyze tank wet stock with gain/loss calculations')

@push('styles')
<link href="{{ asset('css/wet-stock.css') }}" rel="stylesheet">
@endpush

@section('content')
@permission('wet-stock.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-water text-primary me-2"></i>Wet Stock Analysis</h3>
            <p class="text-muted mb-0">Analyze tank stock levels, gains, and losses over time</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-list-ol text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Records</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ $stats['total_records'] }}</h3>
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
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($stats['total_purchase']) }}</h3>
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
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($stats['total_sales']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle {{ $stats['total_gain_loss'] >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-{{ $stats['total_gain_loss'] >= 0 ? 'plus' : 'dash' }}-circle {{ $stats['total_gain_loss'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Gain/Loss</h6>
                        <h3 class="mb-0 {{ $stats['total_gain_loss'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.7rem;">{{ number_format($stats['total_gain_loss']) }}</h3>
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
                    <form method="GET" action="{{ route('admin.wet-stock.index') }}" class="row align-items-end">
                        <div class="mb-3" style="width: 252px;">
                            <label for="tank_id" class="form-label">Select Tank</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-database"></i>
                                </span>
                                <select name="tank_id" id="tank_id" class="form-select border-start-0 searchable-dropdown">
                                    <option selected disabled>All Tanks</option>
                                    @foreach($tanks as $tank)
                                        <option value="{{ $tank->id }}" {{ $tankId == $tank->id ? 'selected' : '' }}>
                                            {{ $tank->tank_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3" style="width: 252px;">
                            <label for="start_date" class="form-label">Start Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-calendar"></i>
                                </span>
                                <input type="date" class="form-control border-start-0" name="start_date" id="start_date" value="{{ $startDate }}">
                            </div>
                        </div>
                        <div class="mb-3" style="width: 252px;">
                            <label for="end_date" class="form-label">End Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-calendar"></i>
                                </span>
                                <input type="date" class="form-control border-start-0" name="end_date" id="end_date" value="{{ $endDate }}">
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('admin.wet-stock.index') }}" class="btn btn-secondary w-100" style="padding: 11px;">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Wet Stock Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Wet Stock Analysis</h5>
                    <div class="d-flex gap-2">
                        <button type="button" id="exportBtn" class="btn btn-success btn-sm">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                        <button type="button" id="printBtn" class="btn btn-secondary btn-sm">
                            <i class="bi bi-printer me-1"></i>Print
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0" id="wetStockTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="ps-3 text-center">Date</th>
                                    <th class="ps-3 text-center">Tank</th>
                                    <th class="ps-3 text-center">O/Stock</th>
                                    <th class="ps-3 text-center">Purchase</th>
                                    <th class="ps-3 text-center">Sales</th>
                                    <th class="ps-3 text-center">Book Stock</th>
                                    <th class="ps-3 text-center">Dip Value(mm)</th>
                                    <th class="ps-3 text-center">Dip Stock</th>
                                    <th class="ps-3 text-center">Gain/Loss</th>
                                    <th class="ps-3 text-center">Total G/L</th>
                                    <th class="ps-3 text-center">Variance</th>
                                    <th class="ps-3 text-center">Cumulative Sale</th>
                                    <th class="ps-3 text-center">Cumulative Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wetStockData as $row)
                                <tr class="{{ $row['tank_name'] === 'TOTAL' ? 'table-primary fw-bold' : '' }}">
                                    <td class="text-center">
                                        @if($row['date'])
                                            {{ $row['date']->format('d-m-Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($row['tank_name'] === 'TOTAL')
                                            <strong>{{ $row['tank_name'] }}</strong>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <span class="text-primary">{{ substr($row['tank_name'], 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <span class="fw-medium">{{ $row['tank_name'] }}</span>
                                                    @if($row['product_name'])
                                                        <small class="text-muted d-block">{{ $row['product_name'] }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $row['opening_stock'] !== null ? number_format($row['opening_stock'], 2) : '-' }}</td>
                                    <td class="text-end">{{ $row['purchase_stock'] !== null ? number_format($row['purchase_stock']) : '-' }}</td>
                                    <td class="text-end">{{ $row['sales_stock'] !== null ? number_format($row['sales_stock']) : '-' }}</td>
                                    <td class="text-end">{{ $row['book_stock'] !== null ? number_format($row['book_stock'], 2) : '-' }}</td>
                                    <td class="text-center">
                                        @if($row['dip_value'] !== null)
                                            <span class="badge bg-info">{{ number_format($row['dip_value']) }} mm</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $row['dip_stock'] !== null ? number_format($row['dip_stock'], 2) : '-' }}</td>
                                    <td class="text-end">
                                        @if($row['gain_loss'] !== null)
                                            <span class="{{ $row['gain_loss'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                                {{ number_format($row['gain_loss'], 2) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($row['total_gain_loss'] !== null)
                                            <span class="{{ $row['total_gain_loss'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                                {{ number_format($row['total_gain_loss'], 2) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($row['variance'] !== null)
                                            <span class="badge {{ abs($row['variance']) <= 2 ? 'bg-success' : (abs($row['variance']) <= 5 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($row['variance'], 2) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">no sales</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $row['cumulative_sale'] !== null ? number_format($row['cumulative_sale']) : '-' }}</td>
                                    <td class="text-end">
                                        @if($row['cumulative_variance'] !== null)
                                            <span class="badge {{ abs($row['cumulative_variance']) <= 2 ? 'bg-success' : (abs($row['cumulative_variance']) <= 5 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($row['cumulative_variance'], 2) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">no sales</span>
                                        @endif
                                    </td>
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
@endpermission

<!-- Hidden data for JavaScript -->
<script type="text/javascript">
    window.wetStockData = @json($wetStockData);
    window.routes = {
        export: '{{ route("admin.wet-stock.export") }}'
    };
    window.filterParams = {
        tank_id: '{{ $tankId }}',
        start_date: '{{ $startDate }}',
        end_date: '{{ $endDate }}'
    };
</script>
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
    .form-control:focus, .form-select:focus {
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
<script src="{{ asset('js/wet-stock.js') }}?v=1.1"></script>
<script>
    $(document).ready(function() {
        $('#wetStockTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            pageLength: 50,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [2,3,4,5,7,8,9,11], className: 'text-end' },
                { targets: [6,10,12], className: 'text-center' }
            ]
        });

        document.title = "Wet Stock Analysis";
    });
</script>
@endpush
