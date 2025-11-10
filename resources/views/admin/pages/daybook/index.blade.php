@extends('admin.layout.master')

@section('title', 'Daybook')
@section('description', 'Daily Business Summary and Transactions')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/daybook.css') }}">
@endsection

@section('content')
@permission('daybook.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Daybook</h3>
            <p class="text-muted mb-0">Daily Business Summary from {{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }}</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
                    <!-- Current Cash Balance -->
                    <div class="cash-balance-widget d-flex align-items-center">
                        <div class="cash-balance-icon me-3">
                            <i class="bi bi-cash-coin text-primary fs-4"></i>
                        </div>
                        <div class="cash-balance-content">
                            <small class="text-muted d-block mb-0">Current Cash Balance</small>
                            <span class="cash-balance-amount text-primary fw-bold">Rs {{ number_format($currentCash, 0, '', ',') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.daybook.index') }}" method="get" class="row align-items-end">
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
                                <i class="bi bi-search me-2"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.daybook.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    {{-- <div class="row mb-4">
        <div class="col-12 text-center">
            <div class="btn-group" role="group">
                <button id="printDaybookBtn" class="btn btn-success px-4">
                    <i class="bi bi-printer me-2"></i>Print Report
                </button>
                <button id="exportPDFBtn" class="btn btn-danger px-4">
                    <i class="bi bi-file-pdf me-2"></i>Export PDF
                </button>
                <button id="exportDaybookBtn" class="btn btn-primary px-4">
                    <i class="bi bi-file-excel me-2"></i>Export Excel
                </button>
            </div>
        </div>
    </div> --}}

    <!-- Tabbed Content -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white p-0">
                    <ul class="nav nav-tabs border-bottom-0" id="daybookTabs" role="tablist">
                        <li class="nav-item" style="padding: 6px;" role="presentation">
                            <button class="nav-link active" id="general-view-tab" data-bs-toggle="tab" data-bs-target="#general-view" type="button" role="tab">
                                <i class="bi bi-grid-3x3-gap me-2"></i>General View
                            </button>
                        </li>
                        <li class="nav-item" style="padding: 6px;" role="presentation">
                            <button class="nav-link" id="purchase-tab" data-bs-toggle="tab" data-bs-target="#purchase" type="button" role="tab">
                                <i class="bi bi-cart-plus me-2"></i>Purchase Details
                            </button>
                        </li>
                        <li class="nav-item" style="padding: 6px;" role="presentation">
                            <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                                <i class="bi bi-cart-check me-2"></i>Sales Details
                            </button>
                        </li>
                        <li class="nav-item" style="padding: 6px;" role="presentation">
                            <button class="nav-link" id="credit-sales-tab" data-bs-toggle="tab" data-bs-target="#credit-sales" type="button" role="tab">
                                <i class="bi bi-credit-card me-2"></i>Credit Sales
                            </button>
                        </li>
                        <li class="nav-item" style="padding: 6px;" role="presentation">
                            <button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                                <i class="bi bi-arrow-left-right me-2"></i>Cash Transactions
                            </button>
                        </li>
                        <li class="nav-item" style="padding: 6px;" role="presentation">
                            <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button" role="tab">
                                <i class="bi bi-bank me-2"></i>Bank Transactions
                            </button>
                        </li>
                        <li class="nav-item" style="padding: 6px;" role="presentation">
                            <button class="nav-link" id="journal-tab" data-bs-toggle="tab" data-bs-target="#journal" type="button" role="tab">
                                <i class="bi bi-journal-text me-2"></i>Journal Vouchers
                            </button>
                        </li>
                        <li class="nav-item" style="padding: 6px;" role="presentation">
                            <button class="nav-link" id="wetstock-tab" data-bs-toggle="tab" data-bs-target="#wetstock" type="button" role="tab">
                                <i class="bi bi-droplet me-2"></i>Wet Stock
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="daybookTabsContent">
                        <!-- General View Tab -->
                        <div class="tab-pane fade show active" id="general-view" role="tabpanel">
                            <div class="p-4">
                                @include('admin.pages.daybook.partials.general-view')
                            </div>
                        </div>

                        <!-- Purchase Tab -->
                        <div class="tab-pane fade" id="purchase" role="tabpanel">
                            <div class="p-4">
                                <!-- Purchase Details Card -->
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.purchase-details')
                                    </div>
                                </div>

                                <!-- Purchase Summary Card -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.purchase-summary')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Tab -->
                        <div class="tab-pane fade" id="sales" role="tabpanel">
                            <div class="p-4">
                                <!-- Sales Details Card -->
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.sales-details')
                                    </div>
                                </div>

                                <!-- Sales Summary Card -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.sales-summary')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Sales Tab -->
                        <div class="tab-pane fade" id="credit-sales" role="tabpanel">
                            <div class="p-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.credit-sales')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cash Transactions Tab -->
                        <div class="tab-pane fade" id="transactions" role="tabpanel">
                            <div class="p-4">
                                <!-- Cash Receipts Card -->
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.cash-receipts')
                                    </div>
                                </div>

                                <!-- Cash Payments Card -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.cash-payments')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transactions Tab -->
                        <div class="tab-pane fade" id="bank" role="tabpanel">
                            <div class="p-4">
                                <!-- Bank Receiving Card -->
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.bank-receiving')
                                    </div>
                                </div>

                                <!-- Bank Payments Card -->
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.bank-payments')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Journal Vouchers Tab -->
                        <div class="tab-pane fade" id="journal" role="tabpanel">
                            <div class="p-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.journal-vouchers')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Wet Stock Tab -->
                        <div class="tab-pane fade" id="wetstock" role="tabpanel">
                            <div class="p-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        @include('admin.pages.daybook.partials.wet-stock')
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
@endpermission
@endsection

@push('scripts')
<script src="{{ asset('js/daybook-ajax.js') }}?v=1.3"></script>
@endpush
