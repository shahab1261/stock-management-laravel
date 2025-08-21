@extends('admin.layout.master')

@section('title', 'Summary Report')
@section('description', 'Vendor Summary and Financial Overview')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/daybook.css') }}">
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3 class="mb-0"><i class="bi bi-file-earmark-check text-primary me-2"></i>Summary Report</h3>
                <p class="text-muted mb-0">Financial summary from {{ date('d-m-Y', strtotime($startDate)) }} to
                    {{ date('d-m-Y', strtotime($endDate)) }}</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3"
                            style="width: 66px; height: 66px;">
                            <i class="bi bi-arrow-up-circle text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Debit</h6>
                            <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalsRow['debit']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3"
                            style="width: 66px; height: 66px;">
                            <i class="bi bi-arrow-down-circle text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Credit</h6>
                            <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalsRow['credit']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="d-flex justify-content-center align-items-center rounded-circle bg-info bg-opacity-10 p-3 me-3"
                            style="width: 66px; height: 66px;">
                            <i class="bi bi-graph-up text-info" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Sales</h6>
                            <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalsRow['total_sales']) }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="d-flex justify-content-center align-items-center rounded-circle bg-warning bg-opacity-10 p-3 me-3"
                            style="width: 66px; height: 66px;">
                            <i class="bi bi-calculator text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Profit</h6>
                            <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalsRow['total_profit']) }}
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
                        <form action="{{ route('admin.reports.summary') }}" method="get" class="row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $endDate }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="1" {{ $status == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $status == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2 mb-3">
                                <a href="{{ route('admin.reports.summary') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Content -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Vendor Summary</h5>
                        {{-- <div class="d-flex gap-2">
                            <button type="button" id="exportBtn" class="btn btn-success btn-sm">
                                <i class="bi bi-download me-1"></i>Export
                            </button>
                            <button type="button" id="printBtn" class="btn btn-secondary btn-sm">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                        </div> --}}
                    </div>
                    <div class="card-body p-0 pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="summaryTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="ps-3 text-center">#</th>
                                        <th class="ps-3 text-center">A/c Code</th>
                                        <th class="ps-3 text-center">Account Title</th>
                                        <th class="ps-3 text-center">Debit</th>
                                        <th class="ps-3 text-center">Credit</th>
                                        @foreach ($dippableProducts as $product)
                                            <th class="ps-3 text-center">{{ $product->name }} <small>Sale</small></th>
                                        @endforeach
                                        <th class="ps-3 text-center">Total Sale</th>
                                        @foreach ($dippableProducts as $product)
                                            <th class="ps-3 text-center">{{ $product->name }} <small>Purchase</small></th>
                                        @endforeach
                                        <th class="ps-3 text-center">Total Purchase</th>
                                        @foreach ($dippableProducts as $product)
                                            <th class="ps-3 text-center">{{ $product->name }} <small>Profit</small></th>
                                        @endforeach
                                        <th class="ps-3 text-center">Total Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($processedData as $row)
                                        <tr>
                                            <td class="text-center">{{ $row['counter'] }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $row['type'] }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                        <span
                                                            class="text-primary">{{ substr($row['account_name'], 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="fw-medium">{{ $row['account_name'] }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                @if ($row['debit'] > 0)
                                                    <span
                                                        class="text-danger fw-bold">{{ number_format($row['debit']) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($row['credit'] > 0)
                                                    <span
                                                        class="text-success fw-bold">{{ number_format($row['credit']) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            @if ($row['type'] == 'Supplier' || $row['type'] == 'Customer')
                                                @foreach ($dippableProducts as $key => $product)
                                                    <td class="text-end">
                                                        @if (isset($row['sales_data'][$product->id]) && $row['sales_data'][$product->id] > 0)
                                                            <span
                                                                class="badge bg-success">{{ number_format($row['sales_data'][$product->id]) }}</span>
                                                        @else
                                                            <span class="text-muted">0</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-end">
                                                    <span
                                                        class="badge bg-success">{{ number_format($row['total_sales']) }}</span>
                                                </td>

                                                @foreach ($dippableProducts as $key => $product)
                                                    <td class="text-end">
                                                        @if (isset($row['purchase_data'][$product->id]) && $row['purchase_data'][$product->id] > 0)
                                                            <span
                                                                class="badge bg-info">{{ number_format($row['purchase_data'][$product->id]) }}</span>
                                                        @else
                                                            <span class="text-muted">0</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-end">
                                                    <span
                                                        class="badge bg-info">{{ number_format($row['total_purchase']) }}</span>
                                                </td>

                                                @foreach ($dippableProducts as $key => $product)
                                                    <td class="text-end">
                                                        @if (isset($row['profit_data'][$product->id]) && $row['profit_data'][$product->id] > 0)
                                                            <span
                                                                class="badge bg-warning">{{ number_format($row['profit_data'][$product->id]) }}</span>
                                                        @else
                                                            <span class="text-muted">0</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-end">
                                                    <span
                                                        class="badge bg-warning">{{ number_format($row['total_profit']) }}</span>
                                                </td>
                                            @else
                                                @for ($i = 0; $i < count($dippableProducts) * 3 + 3; $i++)
                                                    <td class="text-center">
                                                        <span class="text-muted">-</span>
                                                    </td>
                                                @endfor
                                            @endif
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#summaryTable").DataTable({
                processing: true,
                responsive: false,
                scrollX: true,
                dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100],
                ],
                pageLength: 10,
                order: [
                    [0, "desc"]
                ],
            });

            document.title = "Summary Report";
        });
    </script>
@endpush

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

    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .rounded-circle {
        transition: transform 0.2s ease-in-out;
    }

    .card:hover .rounded-circle {
        transform: scale(1.05);
    }

    /* Remove double borders from table */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th,
    .table td {
        border: 1px solid #dee2e6;
        border-left: none;
        border-right: none;
    }

    .table th:first-child,
    .table td:first-child {
        border-left: 1px solid #dee2e6;
    }

    .table th:last-child,
    .table td:last-child {
        border-right: 1px solid #dee2e6;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }

    .table-primary {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
</style>
