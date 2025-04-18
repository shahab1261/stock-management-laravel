@extends('admin.layout.master')

@section('title', 'Manage Nozzles')
@section('description', 'Manage your nozzle configurations')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-fuel-pump text-primary me-2"></i>Nozzles</h3>
            <p class="text-muted mb-0">Manage your nozzle configurations and readings</p>
        </div>
    </div>

    <!-- Nozzles Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Nozzles</h5>
                    <button type="button" id="addNewNozzleBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Nozzle
                    </button>
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table id="nozzlesTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="ps-3">ID</th>
                                    <th width="15%" class="ps-3">Name</th>
                                    <th width="10%" class="ps-3">Reading</th>
                                    <th width="15%" class="ps-3">Product Associated</th>
                                    <th width="15%" class="ps-3">Tank Associated</th>
                                    <th width="10%" class="ps-3">Notes</th>
                                    <th width="10%" class="ps-3">Created At</th>
                                    <th width="20%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nozzles as $nozzle)
                                <tr>
                                    <td class="ps-3">{{ $nozzle->id }}</td>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($nozzle->name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $nozzle->name }}</span>
                                        </div>
                                    </td>
                                    <td class="ps-3">{{ $nozzle->opening_reading }}</td>
                                    <td class="ps-3">{{ $nozzle->product ? $nozzle->product->name : 'N/A' }}</td>
                                    <td class="ps-3">{{ $nozzle->tank ? $nozzle->tank->tank_name : 'N/A' }}</td>
                                    <td class="ps-3">{{ Str::limit($nozzle->notes, 20) }}
                                    <td class="ps-3">{{ date('d M Y', strtotime($nozzle->created_at)) }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-nozzle me-1"
                                            data-id="{{ $nozzle->id }}"
                                            data-name="{{ $nozzle->name }}"
                                            data-opening_reading="{{ $nozzle->opening_reading }}"
                                            data-product_id="{{ $nozzle->product_id }}"
                                            data-tank_id="{{ $nozzle->tank_id }}"
                                            data-notes="{{ $nozzle->notes }}"
                                            data-tanks="{{ $nozzle->tank }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-nozzle" data-id="{{ $nozzle->id }}">
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

<!-- Add Nozzle Modal -->
<div class="modal fade" id="addNozzleModal" tabindex="-1" aria-labelledby="addNozzleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="addNozzleModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Nozzle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addNozzleForm" action="{{ route('admin.nozzles.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Nozzle Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-fuel-pump"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="name" name="name" placeholder="Enter nozzle name" required>
                        </div>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="product_id" class="form-label fw-medium">Product</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-droplet"></i>
                            </span>
                            <select class="form-select border-start-0" id="product_id" name="product_id">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="product_id-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="tank_id" class="form-label fw-medium">Tank</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-database"></i>
                            </span>
                            <select class="form-select border-start-0" id="tank_id" name="tank_id">

                            </select>
                        </div>
                        <div class="invalid-feedback" id="tank_id-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="opening_reading" class="form-label fw-medium">Opening Reading <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-speedometer"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="opening_reading" name="opening_reading" placeholder="Enter opening reading" required>
                        </div>
                        <div class="invalid-feedback" id="opening_reading-error"></div>
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
                <button type="submit" form="addNozzleForm" class="btn btn-primary add-nozzle">
                    <span class="spinner-border spinner-border-sm d-none me-1 add-nozzle" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Save Nozzle
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Nozzle Modal -->
<div class="modal fade" id="editNozzleModal" tabindex="-1" aria-labelledby="editNozzleModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="editNozzleModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Nozzle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editNozzleForm" action="{{ route('admin.nozzles.update') }}" class="row g-3">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="col-md-6">
                        <label for="edit_name" class="form-label fw-medium">Nozzle Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-fuel-pump"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_name" name="name" placeholder="Enter nozzle name" required>
                        </div>
                        <div class="invalid-feedback" id="edit-name-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_product_id" class="form-label fw-medium">Product</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-droplet"></i>
                            </span>
                            <select class="form-select border-start-0" id="edit_product_id" name="product_id">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ $nozzle->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="edit-product_id-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_tank_id" class="form-label fw-medium">Tank</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-database"></i>
                            </span>
                            <select class="form-select border-start-0" id="edit_tank_id" name="tank_id">
                                <option value="">Select Tank</option>
                            </select>
                        </div>
                        <div class="invalid-feedback" id="edit-tank_id-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_opening_reading" class="form-label fw-medium">Opening Reading <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-speedometer"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="edit_opening_reading" name="opening_reading" placeholder="Enter opening reading" required>
                        </div>
                        <div class="invalid-feedback" id="edit-opening_reading-error"></div>
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
                <button type="submit" form="editNozzleForm" class="btn btn-primary editNozzle">
                    <span class="spinner-border spinner-border-sm d-none me-1 update-btn" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Update Nozzle
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteNozzleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Nozzle
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
        background-color: #3a4cd8;
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
<script src="{{ asset('js/nozzle-ajax.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#nozzlesTable').DataTable({
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
