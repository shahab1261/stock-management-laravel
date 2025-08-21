@extends('admin.layout.master')

@section('title', 'Trial Balance')
@section('description', 'View and manage trial balance report')

@push('styles')
<link href="{{ asset('css/trial-balance.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-list-columns-reverse text-primary me-2"></i>Trial Balance</h3>
            <p class="text-muted mb-0">View account balances and financial position as of {{ date('d M Y', strtotime($endDate)) }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-plus-circle text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Debit</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">Rs {{ number_format($debitTotal) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-danger bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-dash-circle text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Credit</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">Rs {{ number_format($creditTotal) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-info bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-calculator text-info" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Difference</h6>
                        <h3 class="mb-0 {{ $turnoverDifference == 0 ? 'text-success' : 'text-warning' }}" style="font-size: 1.7rem;">
                            Rs {{ number_format(abs($turnoverDifference)) }}
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
                    <form action="{{ route('admin.trial-balance.index') }}" method="get" class="row align-items-end">
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
                            <a href="{{ route('admin.trial-balance.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Trial Balance Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Trial Balance Report</h5>
                    <div class="d-flex gap-2">
                        <button type="button" id="exportBtn" class="btn btn-success d-flex align-items-center">
                            <i class="bi bi-download me-2"></i> Export to Excel
                        </button>
                        <button type="button" id="printBtn" class="btn btn-info d-flex align-items-center">
                            <i class="bi bi-printer me-2"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="trialBalanceTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50" class="ps-3 text-center">#</th>
                                    <th class="ps-3 text-center">A/c Code</th>
                                    <th class="ps-3 text-center">Account Title</th>
                                    <th class="ps-3 text-center">Debit</th>
                                    <th class="ps-3 text-center">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = 1;
                                    $currentType = '';
                                    $typeDebitSum = 0;
                                    $typeCreditSum = 0;
                                    $groupedEntries = $trialBalanceEntries->groupBy('type');
                                @endphp

                                @foreach($groupedEntries as $type => $entries)
                                    @php
                                        $typeDebitSum = 0;
                                        $typeCreditSum = 0;
                                    @endphp

                                    @foreach($entries as $entry)
                                        @php
                                            // Calculate type totals using same rule as old system:
                                            // if debit > credit -> add abs(final_balance) to type debit sum, else to credit sum
                                            if (($entry->debit ?? 0) > ($entry->credit ?? 0)) {
                                                $typeDebitSum += abs($entry->final_balance);
                                            } else {
                                                $typeCreditSum += abs($entry->final_balance);
                                            }
                                        @endphp

                                        <tr>
                                            <td class="text-center">{{ $counter }}</td>
                                            <td class="text-center">{{ $entry->type }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                        <span class="text-primary">{{ substr($entry->account_name, 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="fw-medium">{{ $entry->account_name }}</span>
                                                        @if($entry->type == 'Product' && $entry->product_stock)
                                                            <small class="text-muted d-block">(Stock: {{ number_format($entry->product_stock) }})</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                @if(($entry->debit ?? 0) > ($entry->credit ?? 0))
                                                    <span class="text-success fw-bold">{{ number_format(abs($entry->final_balance)) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if(($entry->debit ?? 0) > ($entry->credit ?? 0))
                                                    <span class="text-muted">-</span>
                                                @else
                                                    <span class="text-danger fw-bold">{{ number_format(abs($entry->final_balance)) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $counter++; @endphp
                                    @endforeach

                                    @if(!in_array($type, ['Cash', 'MP']))
                                        <!-- Type Summary Row -->
                                        <tr class="table-light border-top-2">
                                            <td class="text-center fw-bold">{{ $counter }}</td>
                                            <td class="text-center">-</td>
                                            <td class="fw-bold">{{ $type }} Total</td>
                                            <td class="text-end fw-bold">
                                                @if($typeDebitSum > 0)
                                                    {{ number_format($typeDebitSum) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold">
                                                @if($typeCreditSum > 0)
                                                    {{ number_format($typeCreditSum) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @php $counter++; @endphp
                                    @endif
                                @endforeach

                                <!-- Grand Total Row -->
                                <tr class="table-primary border-top-3">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="text-center">-</td>
                                    <td class="fw-bold">Grand Total</td>
                                    <td class="text-end fw-bold">{{ number_format($debitTotal) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($creditTotal) }}</td>
                                </tr>
                                @php $counter++; @endphp

                                <!-- Difference Row -->
                                @if($turnoverDifference != 0)
                                <tr class="table-warning">
                                    <td class="text-center fw-bold">{{ $counter }}</td>
                                    <td class="text-center">-</td>
                                    <td class="fw-bold">Turnover Difference</td>
                                    <td class="text-end fw-bold">-</td>
                                    <td class="text-end fw-bold text-warning">Rs {{ number_format(abs($turnoverDifference), 2) }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
<script src="{{ asset('js/trial-balance.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#trialBalanceTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            pageLength: 25,
            order: [[0, 'asc']],
            columnDefs: [
                { targets: [3, 4], className: 'text-end' },
                { targets: 0, type: 'num' }
            ]
        });

        document.title = "Trial Balance";
    });
</script>
@endpush
