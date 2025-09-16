@extends('admin.layout.master')

@section('title', 'Customer Management')
@section('description', 'Manage your customers')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section with Stats -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-people text-primary me-2"></i>Customers</h3>
            <p class="text-muted mb-0">Manage your customers and their information</p>
        </div>
    </div>

    <!-- Customer Cards Overview -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Customers</h6>
                        <h3 class="mb-0">{{ count($customers) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Active Customers</h6>
                        <h3 class="mb-0">{{ $customers->where('status', 1)->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-danger bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-x-circle text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Inactive Customers</h6>
                        <h3 class="mb-0">{{ $customers->where('status', 0)->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Customers</h5>
                    @permission('management.customers.create')
                    <button type="button" id="addNewCustomerBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Customer
                    </button>
                    @endpermission
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0" id="customersTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50" class="ps-3 text-center">#</th>
                                    <th class="ps-3">Name</th>
                                    <th class="ps-3">Company Name</th>
                                    <th class="ps-3">Email</th>
                                    <th class="ps-3">Phone</th>
                                    <th class="ps-3">Credit Limit</th>
                                    <th class="ps-3">Bank Account</th>
                                    <th class="ps-3">Address</th>
                                    <th class="ps-3">Balance</th>
                                    <th class="ps-3">Status</th>
                                    <th class="ps-3">Created Date</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $key => $customer)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($customer->name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $customer->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $customer->company_name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ number_format($customer->credit_limit, 2) }}</td>
                                    <td>{{ $customer->bank_account_number }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>{{ number_format($customer->balance, 2) }}</td>
                                    <td>
                                        @if($customer->status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ date('d M Y', strtotime($customer->created_at)) }}</td>
                                    <td class="text-center">
                                        @permission('management.customers.edit')
                                        <button class="btn btn-sm btn-outline-primary edit-btn me-1"
                                            data-id="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}"
                                            data-company="{{ $customer->company_name }}"
                                            data-email="{{ $customer->email }}"
                                            data-phone="{{ $customer->phone }}"
                                            data-address="{{ $customer->address }}"
                                            data-credit="{{ $customer->credit_limit }}"
                                            data-account="{{ $customer->bank_account_number }}"
                                            data-balance="{{ $customer->balance }}"
                                            data-notes="{{ $customer->notes }}"
                                            data-status="{{ $customer->status }}"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        @endpermission
                                        @permission('management.customers.delete')
                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $customer->id }}" title="Delete">
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

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="modalTitle">
                    <i class="bi bi-person-plus me-2"></i>Add New Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="customerForm" class="row g-3" action="{{ route('admin.customers.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="customer_id" name="id">

                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Customer Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="name" name="name" placeholder="Enter customer name">
                        </div>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="company_name" class="form-label fw-medium">Company Name</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-building"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="company_name" name="company_name" placeholder="Enter company name">
                        </div>
                        <div class="invalid-feedback" id="company_name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="Enter email address">
                        </div>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="phone" name="phone" placeholder="Enter phone number">
                        </div>
                        <div class="invalid-feedback" id="phone-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="credit_limit" class="form-label fw-medium">Credit Limit</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-cash"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="credit_limit" name="credit_limit" placeholder="Enter credit limit">
                        </div>
                        <div class="invalid-feedback" id="credit_limit-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="bank_account_number" class="form-label fw-medium">Bank Account Number</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-bank"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="bank_account_number" name="bank_account_number" placeholder="Enter bank account number">
                        </div>
                        <div class="invalid-feedback" id="bank_account_number-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                        <div class="d-flex mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusActive" value="1" checked>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusInactive" value="0">
                                <label class="form-check-label" for="statusInactive">Inactive</label>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="status-error"></div>
                    </div>

                    <div class="col-md-12">
                        <label for="address" class="form-label fw-medium">Address</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="address" name="address" rows="2" placeholder="Enter customer address"></textarea>
                        </div>
                        <div class="invalid-feedback" id="address-error"></div>
                    </div>

                    <div class="col-md-12">
                        <label for="notes" class="form-label fw-medium">Notes</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-sticky"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="notes" name="notes" rows="2" placeholder="Enter additional notes"></textarea>
                        </div>
                        <div class="invalid-feedback" id="notes-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" style="background-color: #fdfdfd;" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" id="saveBtn" class="btn btn-primary px-4">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1"></i> <span id="saveBtnText">Save</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editCustomerForm" class="row g-3" action="{{ route('admin.customers.update') }}" method="POST">
                    @csrf
                    <input type="hidden" id="customer_id" name="id">

                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Customer Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="name" name="name" placeholder="Enter customer name">
                        </div>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="company_name" class="form-label fw-medium">Company Name</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-building"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="company_name" name="company_name" placeholder="Enter company name">
                        </div>
                        <div class="invalid-feedback" id="company_name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="Enter email address">
                        </div>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="phone" name="phone" placeholder="Enter phone number">
                        </div>
                        <div class="invalid-feedback" id="phone-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="credit_limit" class="form-label fw-medium">Credit Limit</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-cash"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="credit_limit" name="credit_limit" placeholder="Enter credit limit">
                        </div>
                        <div class="invalid-feedback" id="credit_limit-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="bank_account_number" class="form-label fw-medium">Bank Account Number</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-bank"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="bank_account_number" name="bank_account_number" placeholder="Enter bank account number">
                        </div>
                        <div class="invalid-feedback" id="bank_account_number-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                        <div class="d-flex mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="editStatusActive" value="1">
                                <label class="form-check-label" for="editStatusActive">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="editStatusInactive" value="0">
                                <label class="form-check-label" for="editStatusInactive">Inactive</label>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="status-error"></div>
                    </div>

                    <div class="col-md-12">
                        <label for="address" class="form-label fw-medium">Address</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="address" name="address" rows="2" placeholder="Enter customer address"></textarea>
                        </div>
                        <div class="invalid-feedback" id="address-error"></div>
                    </div>

                    <div class="col-md-12">
                        <label for="notes" class="form-label fw-medium">Notes</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-sticky"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="notes" name="notes" rows="2" placeholder="Enter additional notes"></textarea>
                        </div>
                        <div class="invalid-feedback" id="notes-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" style="background-color: #fdfdfd;" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" id="updateBtn" class="btn btn-primary px-4">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1"></i> Update
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
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Are you sure?</h5>
                <p class="text-muted mb-0">You won't be able to revert this action!</p>
                <input type="hidden" id="delete_customer_id">
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
</style>
@endsection

@push('scripts')
<script src="{{ asset('js/customer-ajax.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#customersTable').DataTable({
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
