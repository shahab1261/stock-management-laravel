@extends('admin.layout.master')

@section('title', 'Roles & Permissions Management')
@section('description', 'Manage system roles and permissions')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-shield-lock text-primary me-2"></i>Roles & Permissions Management</h3>
            <p class="text-muted mb-0">Manage system roles and assign permissions</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <ul class="nav nav-tabs card-header-tabs" id="managementTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab">
                                <i class="bi bi-people me-2"></i>Roles Management
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                                <i class="bi bi-person-gear me-2"></i>User Role Assignment
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content" id="managementTabsContent">
                        <!-- Roles Management Tab -->
                        <div class="tab-pane fade show active" id="roles" role="tabpanel">
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0"><i class="bi bi-shield me-2"></i>System Roles</h5>
                                    <button type="button" id="addNewRoleBtn" class="btn btn-primary d-flex align-items-center">
                                        <i class="bi bi-plus-circle me-2"></i> Add New Role
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table id="rolesTable" class="table table-hover table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Role Name</th>
                                                <th class="text-center">Users Count</th>
                                                <th>Permissions</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roles as $key => $role)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            <span class="text-primary">{{ substr($role->name, 0, 1) }}</span>
                                                        </div>
                                                        <span class="fw-medium">{{ $role->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ $role->users_count ?? 0 }} users</span>
                                                </td>
                                                <td>
                                                    <div class="permission-tags">
                                                        @foreach($role->permissions->take(3) as $permission)
                                                            <span class="badge bg-light text-dark me-1">{{ $permission->name }}</span>
                                                        @endforeach
                                                        @if($role->permissions->count() > 3)
                                                            <span class="badge bg-secondary">+{{ $role->permissions->count() - 3 }} more</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($role->name !== 'SuperAdmin')
                                                    <button type="button" class="btn btn-sm btn-outline-primary edit-role me-1"
                                                        data-id="{{ $role->id }}"
                                                        data-name="{{ $role->name }}"
                                                        data-permissions="{{ $role->permissions->pluck('name') }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-role" data-id="{{ $role->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- User Role Assignment Tab -->
                        <div class="tab-pane fade" id="users" role="tabpanel">
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i>User Role Assignment</h5>
                                </div>

                                <div class="table-responsive">
                                    <table id="usersTable" class="table table-hover table-bordered align-middle mb-0 w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">User Name</th>
                                                <th class="text-center">Email</th>
                                                <th class="text-center">Current Role</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $key => $user)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                            <span class="text-primary">{{ substr($user->name, 0, 1) }}</span>
                                                        </div>
                                                        <span class="fw-medium">{{ $user->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $user->email }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ $user->role_name }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $user->status_badge_class }}">{{ $user->status_text }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-success assign-role"
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->name }}"
                                                        data-current-role="{{ $user->role_name }}">
                                                        <i class="bi bi-person-plus me-1"></i>Assign Role
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
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="addRoleModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Role
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addRoleForm" action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="role_name" class="form-label fw-medium">Role Name <span class="text-danger">*</span></label>
                            <div class="input-group mb-0">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-shield"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="role_name" name="name" placeholder="Enter role name" required>
                            </div>
                            <div class="invalid-feedback" id="role-name-error"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label fw-medium">Permissions <span class="text-danger">*</span></label>
                            <div class="permissions-container border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <div class="row">
                                    @php
                                        $permissionGroups = [
                                            // 'Dashboard' removed: dashboard is accessible to all users
                                            'Daybook' => ['daybook.view'],
                                            'Purchase' => ['purchase.view', 'purchase.create', 'purchase.edit', 'purchase.delete'],
                                            'Sales' => ['sales.view', 'sales.create', 'sales.edit', 'sales.delete', 'sales.nozzle.view', 'sales.nozzle.create', 'sales.nozzle.delete', 'sales.lubricant.view', 'sales.lubricant.create', 'sales.lubricant.delete', 'sales.credit.view', 'sales.credit.create', 'sales.credit.edit', 'sales.credit.delete'],
                                            'Journal' => ['journal.view', 'journal.create', 'journal.edit', 'journal.delete'],
                                            'Trial Balance' => ['trial-balance.view', 'trial-balance.export'],
                                            'Profit' => ['profit.view', 'profit.update-rates'],
                                            'Dips' => ['dips.view', 'dips.create', 'dips.edit', 'dips.delete'],
                                            'Wet Stock' => ['wet-stock.view', 'wet-stock.export'],
                                            'Billing' => ['billing.view', 'billing.export'],
                                            'Payments' => ['payments.bank-receiving.view', 'payments.bank-receiving.create', 'payments.bank-payments.view', 'payments.bank-payments.create', 'payments.cash-receiving.view', 'payments.cash-receiving.create', 'payments.cash-payments.view', 'payments.cash-payments.create', 'payments.transaction.delete'],
                                            'Ledgers' => ['ledger.product.view', 'ledger.supplier.view', 'ledger.customer.view', 'ledger.bank.view', 'ledger.cash.view', 'ledger.mp.view', 'ledger.expense.view', 'ledger.income.view', 'ledger.employee.view'],
                                            'History' => ['history.purchases.view', 'history.sales.view', 'history.bank-receivings.view', 'history.bank-payments.view', 'history.cash-receipts.view', 'history.cash-payments.view', 'history.journal-vouchers.view'],
                                            'Reports' => ['reports.account-history.view', 'reports.all-stocks.view', 'reports.summary.view', 'reports.purchase-transport.view', 'reports.sale-transport.view'],
                                            'Management' => ['management.customers.view', 'management.customers.create', 'management.customers.edit', 'management.customers.delete', 'management.banks.view', 'management.banks.create', 'management.banks.edit', 'management.banks.delete', 'management.tanklari.view', 'management.tanklari.create', 'management.tanklari.edit', 'management.tanklari.delete', 'management.drivers.view', 'management.drivers.create', 'management.drivers.edit', 'management.drivers.delete', 'management.expenses.view', 'management.expenses.create', 'management.expenses.edit', 'management.expenses.delete', 'management.incomes.view', 'management.incomes.create', 'management.incomes.edit', 'management.incomes.delete', 'management.nozzles.view', 'management.nozzles.create', 'management.nozzles.edit', 'management.nozzles.delete', 'management.suppliers.view', 'management.suppliers.create', 'management.suppliers.edit', 'management.suppliers.delete', 'management.users.view', 'management.users.create', 'management.users.edit', 'management.users.delete', 'management.terminals.view', 'management.terminals.create', 'management.terminals.edit', 'management.terminals.delete', 'management.employees.view', 'management.employees.create', 'management.employees.edit', 'management.employees.delete', 'management.transports.view', 'management.transports.create', 'management.transports.edit', 'management.transports.delete', 'management.products.view', 'management.products.create', 'management.products.edit', 'management.products.delete', 'management.tanks.view', 'management.tanks.create', 'management.tanks.edit', 'management.tanks.delete', 'management.settings.view', 'management.settings.edit', 'management.date-lock.view', 'management.date-lock.edit', 'system_locked'],
                                            'Profile' => ['profile.view', 'profile.edit', 'profile.change-password'],
                                            'Logs' => ['logs.view'],
                                        ];
                                    @endphp

                                    @foreach($permissionGroups as $groupName => $groupPermissions)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border">
                                                <div class="card-header bg-light py-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input group-select" type="checkbox" id="group_{{ $loop->index }}" data-group="{{ $groupName }}">
                                                        <label class="form-check-label fw-medium" for="group_{{ $loop->index }}">
                                                            {{ $groupName }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="card-body py-2">
                                                    @foreach($groupPermissions as $permission)
                                                        @php
                                                            $permissionModel = $permissions->where('name', $permission)->first();
                                                        @endphp
                                                        @if($permissionModel)
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission }}" id="perm_{{ $permissionModel->id }}">
                                                                <label class="form-check-label small" for="perm_{{ $permissionModel->id }}">
                                                                    {{ ucwords(str_replace(['.', '-', '_'], ' ', $permission)) }}
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addRoleForm" class="btn btn-primary addRoleBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Add Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="editRoleModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Role
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editRoleForm" action="{{ route('admin.roles.update') }}" method="POST">
                    @csrf
                    <input type="hidden" id="edit_role_id" name="id">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="edit_role_name" class="form-label fw-medium">Role Name <span class="text-danger">*</span></label>
                            <div class="input-group mb-0">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-shield"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="edit_role_name" name="name" placeholder="Enter role name" required>
                            </div>
                            <div class="invalid-feedback" id="edit-role-name-error"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="form-label fw-medium">Permissions <span class="text-danger">*</span></label>
                            <div class="permissions-container border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                <div class="row">
                                    @foreach($permissionGroups as $groupName => $groupPermissions)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border">
                                                <div class="card-header bg-light py-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input edit-group-select" type="checkbox" id="edit_group_{{ $loop->index }}" data-group="{{ $groupName }}">
                                                        <label class="form-check-label fw-medium" for="edit_group_{{ $loop->index }}">
                                                            {{ $groupName }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="card-body py-2">
                                                    @foreach($groupPermissions as $permission)
                                                        @php
                                                            $permissionModel = $permissions->where('name', $permission)->first();
                                                        @endphp
                                                        @if($permissionModel)
                                                            <div class="form-check">
                                                                <input class="form-check-input edit-permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission }}" id="edit_perm_{{ $permissionModel->id }}">
                                                                <label class="form-check-label small" for="edit_perm_{{ $permissionModel->id }}">
                                                                    {{ ucwords(str_replace(['.', '-', '_'], ' ', $permission)) }}
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editRoleForm" class="btn btn-primary editRoleBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Update Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Role Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success bg-opacity-10 border-0">
                <h5 class="modal-title" id="assignRoleModalLabel">
                    <i class="bi bi-person-plus me-2"></i>Assign Role to User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="assignRoleForm" action="{{ route('admin.roles.assign') }}" method="POST">
                    @csrf
                    <input type="hidden" id="assign_user_id" name="user_id">

                    <div class="mb-3">
                        <label class="form-label fw-medium">User</label>
                        <input type="text" class="form-control" id="assign_user_name" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Current Role</label>
                        <input type="text" class="form-control" id="assign_current_role" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="assign_role_id" class="form-label fw-medium">Assign Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="assign_role_id" name="role_id" required>
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                @if($role->name !== 'Employee')
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="assign-role-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="assignRoleForm" class="btn btn-success assignRoleBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-check-circle me-1 submit-icon"></i>Assign Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Role
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Are you sure?</h5>
                <p class="text-muted mb-0">You won't be able to revert this action!</p>
                <input type="hidden" id="delete_role_id">
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-light" style="background-color: #fdfdfd;" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" id="confirmDeleteRole" class="btn btn-danger px-4">
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
    .permission-tags {
        max-width: 300px;
    }
    .permissions-container {
        background-color: #f8f9fa;
    }
    .card-header .form-check {
        margin-bottom: 0;
    }
    .card-body .form-check {
        margin-bottom: 0.25rem;
    }
    .card-body .form-check:last-child {
        margin-bottom: 0;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #4154f1;
        background-color: transparent;
        border-bottom: 2px solid #4154f1;
    }
    .nav-tabs .nav-link:hover {
        border: none;
        color: #4154f1;
    }
</style>
@endsection

@push('scripts')
<script src="{{ asset('js/roles-permissions-ajax.js') }}"></script>
@endpush
