@extends('admin.layout.master')

@section('title', 'Journal Voucher')
@section('description', 'Manage journal vouchers and entries')

@push('styles')
<link href="{{ asset('css/journal-voucher.css') }}" rel="stylesheet">
@endpush

@section('content')
@permission('journal.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-journal-text text-primary me-2"></i>Journal Voucher</h3>
            <p class="text-muted mb-0">Create and manage journal entries for accounting transactions</p>
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
                        <h3 class="mb-0" style="font-size: 1.7rem;">Rs {{ number_format($totalDebit) }}</h3>
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
                        <h3 class="mb-0" style="font-size: 1.7rem;">Rs {{ number_format($totalCredit) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle  {{ ($totalDebit - $totalCredit) == 0 ? 'bg-primary' : 'bg-success' }} bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-currency-dollar {{ ($totalDebit - $totalCredit) == 0 ? 'text-primary' : 'text-success' }}" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Balance</h6>
                        <h3 class="mb-0 {{ ($totalDebit - $totalCredit) == 0 ? 'text-primary' : 'text-success' }}" style="font-size: 1.7rem;" style="font-size: 1.7rem;">
                            Rs {{ number_format(abs($totalDebit - $totalCredit)) }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Journal Entries Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Journal Entries</h5>
                    <button type="button" id="addJournalBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Journal Entry
                    </button>
                </div>
                                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0" id="journalTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50" class="ps-3 text-center">#</th>
                                    <th class="ps-3 text-center">Voucher ID</th>
                                    <th class="ps-3 text-center">Date</th>
                                    <th class="ps-3 text-center">Account</th>
                                    <th class="ps-3 text-center">Debit</th>
                                    <th class="ps-3 text-center">Credit</th>
                                    <th class="ps-3 text-center">Description</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($journalEntries as $key => $entry)
                                {{-- @dump($entry) --}}
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $entry->voucher_id ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $entry->transaction_date->format('d M Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($entry->vendor_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <span class="fw-medium">{{ $entry->vendor_name }}</span>
                                                <span class="badge bg-secondary ms-2">{{ $entry->vendor_type_name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($entry->debit_credit == 2)
                                            <span class="text-success fw-bold">{{ number_format($entry->amount, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($entry->debit_credit == 1)
                                            <span class="text-danger fw-bold">{{ number_format($entry->amount, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $entry->description }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger delete-btn p-2"
                                                data-id="{{ $entry->id }}"
                                                data-voucher-id="{{ $entry->voucher_id }}"
                                                title="Delete Voucher">
                                            <i class="bi bi-trash"></i>
                                        </button>
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

<!-- Journal Entry Modal -->
<div class="modal fade" id="journalModal" tabindex="-1" aria-labelledby="journalModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title">
                    <i class="bi bi-journal-plus me-2"></i>Create Journal Entry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="journalForm" class="row g-3">
                    @csrf

                    <!-- Date Field -->
                    <div class="col-md-3">
                        <label for="journal_date" class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-calendar"></i>
                            </span>
                            <input type="date" class="form-control border-start-0" id="journal_date"
                                   value="{{ $dateLock }}" max="{{ date('Y-m-d') }}" disabled required>
                        </div>
                    </div>

                    <!-- Dynamic Entries Container -->
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Journal Entries</h6>
                                    <button type="button" class="btn btn-sm btn-success" id="add_party_btn">
                                        <i class="bi bi-plus-circle me-1"></i> Add Entry
                                    </button>
                                </div>
                                <div id="append_parties">
                                    <!-- Dynamic entries will be added here -->
                                </div>

                                <!-- Totals Row -->
                                <div class="row mt-4 pt-3 border-top">
                                    <div class="col-md-2">
                                        <strong>Total:</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <strong class="text-success" id="debit_sum_div">Rs 0.00</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <strong class="text-danger" id="credit_sum_div">Rs 0.00</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info mb-0 py-2" id="balance_alert">
                                            <small><i class="bi bi-info-circle me-1"></i>Balance: <span id="balance_amount">Rs 0.00</span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary" id="transaction_btn" form="journalForm" disabled>
                    <i class="bi bi-check-circle me-1"></i> Submit Journal Entry
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Journal Entry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Delete Journal Voucher</h5>
                <div id="voucher-details" class="mb-3" style="display: none;">
                    <p class="text-muted mb-2">Voucher ID: <strong id="voucher-id-display"></strong></p>
                    <p class="text-muted mb-2">Total Entries: <strong id="total-entries-display"></strong></p>
                    <div id="entries-list" class="text-start" style="max-height: 200px; overflow-y: auto;">
                        <!-- Entries will be loaded here -->
                    </div>
                </div>
                <p class="text-muted mb-0">This will delete the entire voucher and all related entries. You won't be able to revert this action!</p>
                <input type="hidden" id="delete_entry_id">
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-light" style="background-color: #fdfdfd;" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger px-4">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
    </div>

<!-- Hidden vendor data for JavaScript -->
<script type="text/javascript">
    window.vendorData = {
        suppliers: @json($suppliers),
        customers: @json($customers),
        products: @json($products),
        banks: @json($banks),
        expenses: @json($expenses),
        incomes: @json($incomes)
    };

    window.routes = {
        store: '{{ route("admin.journal.store") }}',
        delete: '{{ route("admin.journal.destroy", ":id") }}',
        getVendors: '{{ route("admin.journal.vendors") }}',
        getVoucherDetails: '{{ route("admin.journal.voucher-details", ":id") }}'
    };
</script>
@endsection

{{-- <style>
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
    .btn-primary {
        background-color: #4154f1;
        border-color: #4154f1;
    }
    .btn-primary:hover, .btn-primary:focus {
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
    .btn.btn-primary:hover {
        background-color: #ffffff;
        border-color: #4154f1;
        color: #4154f1;
    }
</style> --}}
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
<script src="{{ asset('js/journal-voucher.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#journalTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 10,
            order: [[0, 'asc']],
        });
    });
</script>
@endpush
