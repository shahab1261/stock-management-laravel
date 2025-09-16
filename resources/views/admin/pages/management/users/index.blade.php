@extends('admin.layout.master')

@section('title', 'Manage Users')
@section('description', 'Manage system users')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-people text-primary me-2"></i>Users</h3>
            <p class="text-muted mb-0">Manage system users</p>
        </div>
    </div>

    <!-- Users Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Users</h5>
                    @permission('management.users.create')
                    <button type="button" id="addNewUserBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New User
                    </button>
                    @endpermission
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-hover table-bordered align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="ps-3">#</th>
                                    <th width="15%" class="ps-3">Name</th>
                                    <th width="15%" class="ps-3">Email</th>
                                    <th width="10%" class="ps-3">Phone Number</th>
                                    <th width="15%" class="ps-3">Bank Account Number</th>
                                    <th width="10%" class="ps-3">Address</th>
                                    <th width="10%" class="ps-3">Role</th>
                                    <th width="10%" class="ps-3">Status</th>
                                    <th width="10%" class="ps-3">Registered Date</th>
                                    <th width="10%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $key => $user)
                                <tr>
                                    <td class="ps-3">{{ $key + 1 }}</td>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="ps-3">{{ $user->email }}</td>
                                    <td class="ps-3">{{ $user->phone }}</td>
                                    <td class="ps-3">{{ $user->bank_account_number }}</td>
                                    <td class="ps-3">{{ $user->address }}</td>
                                    <td class="ps-3">
                                        <span class="badge bg-primary">{{ $user->role_name }}</span>
                                    </td>
                                    <td class="ps-3">
                                        @if((int)$user->status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="ps-3">{{ date('d-m-Y', strtotime($user->created_at)) }}</td>
                                    <td class="text-center">
                                        @permission('management.users.edit')
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-user me-1"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-phone="{{ $user->phone }}"
                                            data-bank-account="{{ $user->bank_account_number }}"
                                            data-status="{{ (int)$user->status }}"
                                            data-role="{{ $user->role_name }}"
                                            data-address="{{ $user->address }}"
                                            data-notes="{{ $user->notes }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        @endpermission
                                        @if(Auth::id() != $user->id)
                                        @permission('management.users.delete')
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-user" data-id="{{ $user->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endpermission
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addUserForm" action="{{ route('admin.users.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="name" name="name" placeholder="Enter full name"  >
                        </div>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="Enter email address"  >
                        </div>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="phone" name="phone" placeholder="Enter phone number"  >
                        </div>
                        <div class="invalid-feedback" id="phone-error"></div>
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
                        <label for="password" class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Enter password"  >
                            <button class="btn btn-light border toggle-password" type="button" data-target="#password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="password-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-medium">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="password_confirmation" name="password_confirmation" placeholder="Confirm password"  >
                            <button class="btn btn-light border toggle-password" type="button" data-target="#password_confirmation">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="password_confirmation-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-toggle-on"></i>
                            </span>
                            <select class="form-select border-start-0" id="status" name="status"  >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="invalid-feedback" id="status-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-badge"></i>
                            </span>
                            <select class="form-select border-start-0" id="role" name="role"  >
                                <option value="">Select a role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="role-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="address" class="form-label fw-medium">Address</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="address" name="address" rows="2" placeholder="Enter address"></textarea>
                        </div>
                        <div class="invalid-feedback" id="address-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="notes" class="form-label fw-medium">Notes</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-journal-text"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="notes" name="notes" rows="2" placeholder="Enter notes"></textarea>
                        </div>
                        <div class="invalid-feedback" id="notes-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addUserForm" class="btn btn-primary addUserbtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Add User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editUserForm" action="{{ route('admin.users.update') }}" class="row g-3">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">

                    <div class="col-md-6">
                        <label for="edit_name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_name" name="name" placeholder="Enter full name"  >
                        </div>
                        <div class="invalid-feedback" id="edit-name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="edit_email" name="email" placeholder="Enter email address"  >
                        </div>
                        <div class="invalid-feedback" id="edit-email-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_phone" class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_phone" name="phone" placeholder="Enter phone number"  >
                        </div>
                        <div class="invalid-feedback" id="edit-phone-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_bank_account_number" class="form-label fw-medium">Bank Account Number</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-bank"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_bank_account_number" name="bank_account_number" placeholder="Enter bank account number">
                        </div>
                        <div class="invalid-feedback" id="edit-bank_account_number-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_password" class="form-label fw-medium">Password <small class="text-muted">(Leave blank to keep current)</small></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="edit_password" name="password" placeholder="Enter new password">
                            <button class="btn btn-light border toggle-password" type="button" data-target="#edit_password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="edit-password-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_password_confirmation" class="form-label fw-medium">Confirm Password</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="edit_password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                            <button class="btn btn-light border toggle-password" type="button" data-target="#edit_password_confirmation">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="edit-password_confirmation-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-toggle-on"></i>
                            </span>
                            <select class="form-select border-start-0" id="edit_status" name="status"  >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="invalid-feedback" id="edit-status-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_role" class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-badge"></i>
                            </span>
                            <select class="form-select border-start-0" id="edit_role" name="role"  >
                                <option value="">Select a role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="edit-role-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_address" class="form-label fw-medium">Address</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="edit_address" name="address" rows="2" placeholder="Enter address"></textarea>
                        </div>
                        <div class="invalid-feedback" id="edit-address-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_notes" class="form-label fw-medium">Notes</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-journal-text"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="edit_notes" name="notes" rows="2" placeholder="Enter notes"></textarea>
                        </div>
                        <div class="invalid-feedback" id="edit-notes-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editUserForm" class="btn btn-primary editUserbtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Update User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Are you sure?</h5>
                <p class="text-muted mb-0">You won't be able to revert this action!</p>
                <input type="hidden" id="delete_id">
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-light" style="background-color: #fdfdfd;" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" id="confirmDelete" class="btn btn-danger px-4">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-trash me-1 submit-icon"></i> Delete
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
<script src="{{ asset('js/users-ajax.js') }}"></script>
@endpush
