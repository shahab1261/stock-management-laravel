@extends('admin.layout.master')

@section('title', 'Cash Receipts')
@section('description', 'Manage your cash receipt transactions')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section with Stats -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-cash-coin text-primary me-2"></i>Cash Receipts</h3>
            <p class="text-muted mb-0">Manage your cash receipt transactions and entries</p>
        </div>
    </div>

    <!-- Transaction Cards Overview -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-cash-stack text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Current Cash Balance</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">Rs {{ number_format($currentCash) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-receipt text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Today's Receipts</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ count($transactions) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-currency-dollar text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Amount</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">Rs {{ number_format($transactions->sum('amount')) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Receipts Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Cash Receipts</h5>
                    @permission('payments.cash-receiving.create')
                    <button type="button" id="addNewReceiptBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Receipt
                    </button>
                    @endpermission
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0" id="cashReceiptsTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50" class="ps-3 text-center">#</th>
                                    <th class="ps-3 text-center">Date</th>
                                    <th class="ps-3 text-center">Cash <small class="text-muted">(Debit)</small></th>
                                    <th class="ps-3 text-center">Account <small class="text-muted">(Credit)</small></th>
                                    <th class="ps-3 text-center">Amount</th>
                                    <th class="ps-3 text-center">Description</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $key => $transaction)
                                @php
                                    $vendor = app('App\Http\Controllers\PaymentController')->getVendorByType($transaction->vendor_type, $transaction->vendor_id);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $transaction->tid }}</td>
                                    <td>{{ date('d M Y', strtotime($transaction->transaction_date)) }}</td>
                                    <td>
                                        @if($transaction->payment_type == 1)
                                            <span class="badge bg-primary">Cash</span>
                                        @else
                                            <span class="badge bg-primary">{{ $transaction->bank_name }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($vendor->vendor_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <span class="fw-medium">{{ $vendor->vendor_name }}</span>
                                                <span class="badge bg-secondary ms-2">{{ $vendor->vendor_type }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-medium text-success">Rs {{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->description }}</td>
                                    <td class="text-center">
                                        @permission('payments.transaction.delete')
                                            @php
                                                $ledgerPurchaseType = app('App\\Http\\Controllers\\PaymentController')->getLedgerPurchaseType($transaction->transaction_type, $transaction->payment_type);
                                            @endphp
                                            <button class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="{{ $transaction->tid }}"
                                                data-ledger-type="{{ $ledgerPurchaseType }}"
                                                title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endpermission
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

<!-- Add Receipt Modal -->
<div class="modal fade" id="addReceiptModal" tabindex="-1" aria-labelledby="addReceiptModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="modalTitle">
                    <i class="bi bi-plus-circle me-2"></i>Add New Cash Receipt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="cashReceiptForm" class="row g-3" action="{{ route('admin.payments.cash-receiving.store') }}" method="POST">
                    @csrf

                    <div class="col-md-6">
                        <label for="transaction_date" class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-calendar"></i>
                            </span>
                            <input type="date" class="form-control border-start-0" id="transaction_date" name="transaction_date" value="{{ $dateLock }}" disabled required>
                        </div>
                        <div class="invalid-feedback" id="transaction_date-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="journal_vendor" class="form-label fw-medium">Select Account <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <select id="journal_vendor" name="vendor_id" class="form-select border-start-0" required>
                                <option value="">Select Account</option>
                                @include('admin.pages.payments.partials.all-vendors')
                            </select>
                        </div>
                        <div class="invalid-feedback" id="journal_vendor-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="transaction_amount" class="form-label fw-medium">Amount <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-currency-dollar"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="transaction_amount" name="transaction_amount" value="0" min="0" required>
                        </div>
                        <div class="invalid-feedback" id="transaction_amount-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="transaction_description" class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-chat-text"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="transaction_description" name="transaction_description" rows="1" placeholder="Enter description" required></textarea>
                        </div>
                        <div class="invalid-feedback" id="transaction_description-error"></div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" style="background-color: #fdfdfd;" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" id="saveBtn" class="btn btn-primary px-4">
                            <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                            <i class="bi bi-save me-1"></i> <span id="saveBtnText">Save</span>
                        </button>
                    </div>
                </form>
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
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Transaction
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Are you sure?</h5>
                <p class="text-muted mb-0">You won't be able to revert this action!</p>
                <input type="hidden" id="delete_transaction_id">
                <input type="hidden" id="delete_ledger_type">
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
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-success:hover, .btn-success:focus {
        background-color: #218838;
        border-color: #1e7e34;
    }
    .btn-outline-success {
        color: #28a745;
        border-color: #28a745;
    }
    .btn-outline-success:hover {
        background-color: #28a745;
        border-color: #28a745;
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
@endsection

@push('scripts')
<script src="{{ asset('js/cash-receiving-ajax.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#cashReceiptsTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 10,
            order: [[0, 'desc']],
        });

        document.title = "Cash Receipts";
    });
</script>
@endpush
