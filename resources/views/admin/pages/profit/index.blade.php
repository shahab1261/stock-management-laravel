@extends('admin.layout.master')

@section('title', 'Profit & Loss')
@section('description', 'View profit and loss statement')

@push('styles')
<link href="{{ asset('css/profit.css') }}" rel="stylesheet">
@endpush

@section('content')
@permission('profit.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Profit & Loss Statement</h3>
            <p class="text-muted mb-0">Financial performance from {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-trophy text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Gross Profit</h6>
                        <h3 class="mb-0 text-success" style="font-size: 1.5rem;">Rs {{ number_format($grossProfit) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-plus-circle text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Income</h6>
                        <h3 class="mb-0 text-primary" style="font-size: 1.5rem;">Rs {{ number_format($incomeTotal) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-danger bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-dash-circle text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Expense</h6>
                        <h3 class="mb-0 text-danger" style="font-size: 1.5rem;">Rs {{ number_format($expenseTotal) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-calculator text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Net Profit</h6>
                        <h3 class="mb-0 {{ ($netProfit + $mpProfit) >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.5rem;">
                            Rs {{ number_format($netProfit + $mpProfit) }}
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
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Date Range Filter</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profit.index') }}" method="get" class="row align-items-end">
                        <div class="col-md-4 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-calendar"></i>
                                </span>
                                <input type="date" class="form-control border-start-0" id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-calendar"></i>
                                </span>
                                <input type="date" class="form-control border-start-0" id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="{{ route('admin.profit.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                            </a>
                        </div>
                        @if($enableSettlement)
                            <input type="hidden" name="enable_settlement" value="true">
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Statement Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Profit & Loss Statement</h5>
                    <div class="d-flex gap-2">
                        @if($enableSettlement)
                            <a href="{{ route('admin.profit.index', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Clear Settlement
                            </a>
                        @else
                            <a href="{{ route('admin.profit.index', ['start_date' => $startDate, 'end_date' => $endDate, 'enable_settlement' => 'true']) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-gear me-1"></i>Rate Settlement
                            </a>
                        @endif
                        @permission('profit.update-rates')
                            <button type="button" id="updateRatesBtn" class="btn btn-outline-primary btn-sm" data-url="{{ route('admin.profit.update-rates') }}">
                                <i class="bi bi-arrow-clockwise me-1"></i>Update Rates
                            </button>
                        @endpermission
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
                        <table class="table table-hover align-middle mb-0" id="profitTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50" class="ps-3 text-center">#</th>
                                    <th class="ps-3">Product/Account Name</th>
                                    <th class="ps-3 text-end">Profit/Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter = 1; @endphp

                                {{-- Product Profits --}}
                                @foreach($sheets as $sheet)
                                <tr>
                                    <td class="text-center">{{ $counter }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-success">{{ substr($sheet->product_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <span class="fw-medium">{{ $sheet->product_name }}</span>
                                                <small class="text-muted d-block">Sold stock ({{ number_format($sheet->soldstock) }})</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">Rs {{ number_format($sheet->profit) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endforeach

                                {{-- Gross Profit Total --}}
                                <tr class="table-success">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold">Gross Profit</td>
                                    <td class="text-end fw-bold">Rs {{ number_format($grossProfit) }}</td>
                                </tr>
                                @php $counter++; @endphp

                                {{-- Income Section --}}
                                @if($incomeTransactions->count() > 0)
                                <tr class="table-info">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold text-success">Income</td>
                                    <td class="text-end">-</td>
                                </tr>
                                @php $counter++; @endphp

                                @foreach($incomeTransactions as $income)
                                <tr>
                                    <td class="text-center">{{ $counter }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-info">{{ substr($income->vendor_name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $income->vendor_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">Rs {{ number_format($income->amount) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endforeach

                                @if($incomeTotal > 0)
                                <tr class="table-light">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold">Income Total</td>
                                    <td class="text-end fw-bold">Rs {{ number_format($incomeTotal) }}</td>
                                </tr>
                                @php $counter++; @endphp

                                <tr class="table-light">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold">Gross Net Profit</td>
                                    <td class="text-end fw-bold">Rs {{ number_format($grossProfit + $incomeTotal) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endif
                                @endif

                                {{-- Expense Section --}}
                                @if($expenseTransactions->count() > 0)
                                <tr class="table-warning">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold text-danger">Expense</td>
                                    <td class="text-end">-</td>
                                </tr>
                                @php $counter++; @endphp

                                @foreach($expenseTransactions as $expense)
                                <tr>
                                    <td class="text-center">{{ $counter }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-danger bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-danger">{{ substr($expense->vendor_name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $expense->vendor_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">Rs {{ number_format($expense->amount) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endforeach

                                @if($expenseTotal > 0)
                                <tr class="table-light">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold">Expense Total</td>
                                    <td class="text-end fw-bold">Rs {{ number_format($expenseTotal) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endif
                                @endif

                                {{-- Gain/Loss Section --}}
                                @if($gainPurchase->count() > 0 || $lossSale->count() > 0)
                                <tr class="table-secondary">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold text-warning">Gain / Loss</td>
                                    <td class="text-end">-</td>
                                </tr>
                                @php $counter++; @endphp

                                @foreach($gainPurchase as $gain)
                                <tr>
                                    <td class="text-center">{{ $counter }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-warning">{{ substr($gain->product_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <span class="fw-medium">{{ $gain->product_name }}</span>
                                                <small class="text-muted d-block">{{ number_format($gain->quantity) }} ltr</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">Rs {{ number_format($gain->amount) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endforeach

                                @foreach($lossSale as $loss)
                                <tr>
                                    <td class="text-center">{{ $counter }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-danger bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-danger">{{ substr($loss->product_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <span class="fw-medium">{{ $loss->product_name }}</span>
                                                <small class="text-muted d-block">-{{ number_format($loss->quantity) }} ltr</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">Rs -{{ number_format($loss->amount) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endforeach

                                @if($gainLossTotal != 0)
                                <tr class="table-light">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold">Gain / Loss Total</td>
                                    <td class="text-end fw-bold">Rs {{ number_format($gainLossTotal) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endif
                                @endif

                                {{-- Rate Settlement Section --}}
                                @if($enableSettlement)
                                <tr class="table-info">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold text-success">Rate Settlement Profit</td>
                                    <td class="text-end">-</td>
                                </tr>
                                @php $counter++; @endphp

                                @foreach($settlementRows as $settlement)
                                <tr>
                                    <td class="text-center">{{ $counter }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($settlement->product_name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $settlement->product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ number_format($settlement->profit) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endforeach

                                <tr class="table-light">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold">Total Settlement</td>
                                    <td class="text-end fw-bold">Rs {{ number_format($mpProfit) }}</td>
                                </tr>
                                @php $counter++; @endphp
                                @endif

                                {{-- Net Profit Total --}}
                                <tr class="table-primary">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="fw-bold">Net Profit</td>
                                    <td class="text-end fw-bold {{ ($netProfit + $mpProfit) >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rs {{ number_format($netProfit + $mpProfit) }}
                                    </td>
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
    .modal-content {
        border-radius: 0.5rem;
    }
    .modal-header {
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .input-group-text {
        color: #6c757d;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn-danger:hover, .btn-danger:focus {
        background-color: #c82333;
        border-color: #bd2130;
    }
    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }
    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
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
</style>

@push('scripts')
<script src="{{ asset('js/profit.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#profitTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            pageLength: 100,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [2], className: 'text-end' },
                { targets: 0, type: 'num' }
            ]
        });

        document.title = "Profit Sheet";
    });
</script>
@endpush
