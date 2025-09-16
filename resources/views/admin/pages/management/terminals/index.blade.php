@extends('admin.layout.master')

@section('title', 'Manage Terminals')
@section('description', 'Manage system terminals')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-building text-primary me-2"></i>Terminals</h3>
            <p class="text-muted mb-0">Manage system terminals</p>
        </div>
    </div>

    <!-- Terminals Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Terminals</h5>
                    @permission('management.terminals.create')
                    <button type="button" id="addNewTerminalBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add Terminal
                    </button>
                    @endpermission
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table id="terminalsTable" class="table table-hover table-bordered align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="ps-3">#</th>
                                    <th width="15%" class="ps-3">Name</th>
                                    <th width="30%" class="ps-3">Address</th>
                                    <th width="25%" class="ps-3">Notes</th>
                                    <th width="15%" class="ps-3">Registered Date</th>
                                    <th width="10%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($terminals as $key => $terminal)
                                <tr>
                                    <td class="ps-3 text-start">{{ $key + 1 }}</td>
                                    <td class="ps-3 text-start">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($terminal->name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $terminal->name }}</span>
                                        </div>
                                    </td>
                                    <td class="ps-3 text-start">{{ $terminal->address }}</td>
                                    <td class="ps-3 text-start">{{ $terminal->notes }}</td>
                                    <td class="ps-3 text-start">{{ date('d-m-Y', strtotime($terminal->created_at)) }}</td>
                                    <td class="text-center ps-0">
                                        @permission('management.terminals.edit')
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-terminal me-1"
                                            data-id="{{ $terminal->id }}"
                                            data-name="{{ $terminal->name }}"
                                            data-address="{{ $terminal->address }}"
                                            data-notes="{{ $terminal->notes }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        @endpermission
                                        @permission('management.terminals.delete')
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-terminal" data-id="{{ $terminal->id }}">
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

<!-- Add Terminal Modal -->
<div class="modal fade" id="addTerminalModal" tabindex="-1" aria-labelledby="addTerminalModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="addTerminalModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add Terminal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addTerminalForm" action="{{ route('admin.terminals.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-building"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="name" name="name" placeholder="Enter terminal name"  >
                        </div>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>

                    <div class="col-md-12">
                        <label for="address" class="form-label fw-medium">Address</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="address" name="address" rows="2" placeholder="Enter address"></textarea>
                        </div>
                        <div class="invalid-feedback" id="address-error"></div>
                    </div>

                    <div class="col-md-12">
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
                <button type="submit" form="addTerminalForm" class="btn btn-primary addTerminalBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Submit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Terminal Modal -->
<div class="modal fade" id="editTerminalModal" tabindex="-1" aria-labelledby="editTerminalModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="editTerminalModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Terminal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editTerminalForm" action="{{ route('admin.terminals.update') }}" class="row g-3">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">

                    <div class="col-md-12">
                        <label for="edit_name" class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-building"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_name" name="name" placeholder="Enter terminal name"  >
                        </div>
                        <div class="invalid-feedback" id="edit-name-error"></div>
                    </div>

                    <div class="col-md-12">
                        <label for="edit_address" class="form-label fw-medium">Address</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="edit_address" name="address" rows="2" placeholder="Enter address"></textarea>
                        </div>
                        <div class="invalid-feedback" id="edit-address-error"></div>
                    </div>

                    <div class="col-md-12">
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
                <button type="submit" form="editTerminalForm" class="btn btn-primary editTerminalBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Update
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteTerminalModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Terminal
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
<script src="{{ asset('js/terminals-ajax.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#terminalsTable').DataTable({
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
