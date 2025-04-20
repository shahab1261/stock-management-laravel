@extends('admin.layout.master')

@section('title', 'Transport Management')
@section('description', 'Manage your transport vehicles')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section with Stats -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-truck text-primary me-2"></i>Transports</h3>
            <p class="text-muted mb-0">Manage your transport vehicles and chamber capacities</p>
        </div>
    </div>

    <!-- Transports Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Transports</h5>
                    <div>
                        <button id="addNewTransportBtn" class="btn btn-primary d-flex align-items-center">
                            <i class="bi bi-plus-circle me-2"></i> Add Transport
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="transportsTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50" class="ps-3 text-center">#</th>
                                    <th class="ps-3">Vehicle Name</th>
                                    <th class="ps-3">Driver</th>
                                    <th class="ps-3">Chamber Dip 1</th>
                                    <th class="ps-3">Chamber Capacity 1</th>
                                    <th class="ps-3">Chamber Dip 2</th>
                                    <th class="ps-3">Chamber Capacity 2</th>
                                    <th class="ps-3">Chamber Dip 3</th>
                                    <th class="ps-3">Chamber Capacity 3</th>
                                    <th class="ps-3">Chamber Dip 4</th>
                                    <th class="ps-3">Chamber Capacity 4</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transports as $key => $transport)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($transport->larry_name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $transport->larry_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $transport->driver ? $transport->driver->driver_name : 'Nothing selected' }}</td>
                                    <td>{{ $transport->chamber_dip_one }}</td>
                                    <td>{{ $transport->chamber_capacity_one }}</td>
                                    <td>{{ $transport->chamber_dip_two }}</td>
                                    <td>{{ $transport->chamber_capacity_two }}</td>
                                    <td>{{ $transport->chamber_dip_three }}</td>
                                    <td>{{ $transport->chamber_capacity_three }}</td>
                                    <td>{{ $transport->chamber_dip_four }}</td>
                                    <td>{{ $transport->chamber_capacity_four }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary edit-transport me-1"
                                            data-id="{{ $transport->id }}"
                                            data-name="{{ $transport->larry_name }}"
                                            data-driver="{{ $transport->driver ? $transport->driver->id : '' }}"
                                            data-chamber-dip-one="{{ $transport->chamber_dip_one }}"
                                            data-chamber-capacity-one="{{ $transport->chamber_capacity_one }}"
                                            data-chamber-dip-two="{{ $transport->chamber_dip_two }}"
                                            data-chamber-capacity-two="{{ $transport->chamber_capacity_two }}"
                                            data-chamber-dip-three="{{ $transport->chamber_dip_three }}"
                                            data-chamber-capacity-three="{{ $transport->chamber_capacity_three }}"
                                            data-chamber-dip-four="{{ $transport->chamber_dip_four }}"
                                            data-chamber-capacity-four="{{ $transport->chamber_capacity_four }}"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-transport"
                                            data-id="{{ $transport->id }}"
                                            data-name="{{ $transport->larry_name }}"
                                            title="Delete">
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

<!-- Add Transport Modal -->
<div class="modal fade" id="addTransportModal" tabindex="-1" aria-labelledby="addTransportModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title">
                    <i class="bi bi-truck me-2"></i>Add New Transport
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addTransportForm" class="row g-3" action="{{ route('admin.transports.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <input type="hidden" name="tank_type" value="2">

                    <div class="col-md-6">
                        <label for="larry_name" class="form-label fw-medium">Vehicle Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-truck"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="larry_name" name="larry_name" placeholder="Enter vehicle name">
                        </div>
                        <div class="invalid-feedback" id="larry_name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="driver_id" class="form-label fw-medium">Driver</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <select class="form-select border-start-0" id="driver_id" name="driver_id">
                                <option value="">Nothing selected</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->driver_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="driver_id-error"></div>
                    </div>

                    <div class="col-12">
                        <hr class="text-muted">
                        <h5 class="mb-3">Chamber Details</h5>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="50" class="text-center">Chamber No.</th>
                                        <th>Dip (mm)</th>
                                        <th>Capacity (Ltrs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="chamber_dip_one" name="chamber_dip_one" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="chamber_capacity_one" name="chamber_capacity_one" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="chamber_dip_two" name="chamber_dip_two" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="chamber_capacity_two" name="chamber_capacity_two" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3</td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="chamber_dip_three" name="chamber_dip_three" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="chamber_capacity_three" name="chamber_capacity_three" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">4</td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="chamber_dip_four" name="chamber_dip_four" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="chamber_capacity_four" name="chamber_capacity_four" placeholder="0">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submitAddForm" class="btn btn-primary d-flex align-items-center">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                    <i class="bi bi-save me-1 submit-icon"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Transport Modal -->
<div class="modal fade" id="editTransportModal" tabindex="-1" aria-labelledby="editTransportModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Transport
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editTransportForm" class="row g-3" action="{{ route('admin.transports.update') }}" method="POST">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">

                    <div class="col-md-6">
                        <label for="edit_larry_name" class="form-label fw-medium">Vehicle Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-truck"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_larry_name" name="larry_name" placeholder="Enter vehicle name">
                        </div>
                        <div class="invalid-feedback" id="edit_larry_name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_driver_id" class="form-label fw-medium">Driver</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <select class="form-select border-start-0" id="edit_driver_id" name="driver_id">
                                <option value="">Nothing selected</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->driver_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="edit_driver_id-error"></div>
                    </div>

                    <div class="col-12">
                        <hr class="text-muted">
                        <h5 class="mb-3">Chamber Details</h5>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="50" class="text-center">Chamber No.</th>
                                        <th>Dip (mm)</th>
                                        <th>Capacity (Ltrs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="edit_chamber_dip_one" name="chamber_dip_one" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="edit_chamber_capacity_one" name="chamber_capacity_one" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="edit_chamber_dip_two" name="chamber_dip_two" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="edit_chamber_capacity_two" name="chamber_capacity_two" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3</td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="edit_chamber_dip_three" name="chamber_dip_three" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="edit_chamber_capacity_three" name="chamber_capacity_three" placeholder="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">4</td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="edit_chamber_dip_four" name="chamber_dip_four" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" id="edit_chamber_capacity_four" name="chamber_capacity_four" placeholder="0">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submitEditForm" class="btn btn-primary d-flex align-items-center">
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                    <i class="bi bi-save me-1 submit-icon"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Transport
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Are you sure?</h5>
                <p class="text-muted mb-0">You won't be able to revert this action!</p>
                <input type="hidden" id="delete_transport_id">
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-light" style="background-color: #fdfdfd;" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger px-4">
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
<script src="{{ asset('js/transports-ajax.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#transportsTable').DataTable({
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
