@extends('admin.layout.master')

@section('title', 'Driver Management')
@section('description', 'Manage your drivers')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section with Stats -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-person-badge text-primary me-2"></i>Drivers</h3>
            <p class="text-muted mb-0">Manage your drivers and their information</p>
        </div>
    </div>

    <!-- Driver Cards Overview -->
    {{-- <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-person-badge text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Drivers</h6>
                        <h3 class="mb-0">{{ count($drivers) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Drivers Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Drivers</h5>
                    <button type="button" id="addNewDriverBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Driver
                    </button>
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="driversTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50" class="ps-3 text-center">#</th>
                                    <th class="ps-3">Driver Type</th>
                                    <th class="ps-3">Driver Name</th>
                                    <th class="ps-3">CNIC No</th>
                                    <th class="ps-3">Vehicle No</th>
                                    <th class="ps-3">Mobile Numbers</th>
                                    <th class="ps-3">City</th>
                                    <th class="ps-3">Address</th>
                                    <th class="ps-3">Reference</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($drivers as $key => $driver)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>{{ $driver->driver_type }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($driver->driver_name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $driver->driver_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $driver->cnic }}</td>
                                    <td>{{ $driver->vehicle_no }}</td>
                                    <td>
                                        {{ $driver->first_mobile_no }}
                                        @if($driver->second_mobile_no)
                                        ,<br>{{ $driver->second_mobile_no }}
                                        @endif
                                    </td>
                                    <td>{{ $driver->city }}</td>
                                    <td>{{ $driver->address }}</td>
                                    <td>{{ $driver->reference }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary edit-btn me-1"
                                            data-id="{{ $driver->id }}"
                                            data-driver-type="{{ $driver->driver_type }}"
                                            data-driver-name="{{ $driver->driver_name }}"
                                            data-first-mobile="{{ $driver->first_mobile_no }}"
                                            data-second-mobile="{{ $driver->second_mobile_no }}"
                                            data-cnic="{{ $driver->cnic }}"
                                            data-vehicle="{{ $driver->vehicle_no }}"
                                            data-city="{{ $driver->city }}"
                                            data-address="{{ $driver->address }}"
                                            data-reference="{{ $driver->reference }}"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $driver->id }}" title="Delete">
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

<!-- Add Driver Modal -->
<div class="modal fade" id="addDriverModal" tabindex="-1" aria-labelledby="addDriverModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="addDriverModalLabel">
                    <i class="bi bi-person-plus me-2"></i>Add New Driver
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="driverForm" class="row g-3" action="{{ route('admin.drivers.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="driver_id" name="id">

                    <div class="col-md-6">
                        <label for="driver_type" class="form-label fw-medium">Driver Type <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="driver_type" name="driver_type" placeholder="Enter driver type">
                        </div>
                        <div class="invalid-feedback" id="driver_type-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="driver_name" class="form-label fw-medium">Driver Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="driver_name" name="driver_name" placeholder="Enter driver name">
                        </div>
                        <div class="invalid-feedback" id="driver_name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="first_mobile_no" class="form-label fw-medium">Mobile No. <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="first_mobile_no" name="first_mobile_no" placeholder="Enter primary mobile number">
                        </div>
                        <div class="invalid-feedback" id="first_mobile_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="second_mobile_no" class="form-label fw-medium">Mobile No.2</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="second_mobile_no" name="second_mobile_no" placeholder="Enter secondary mobile number">
                        </div>
                        <div class="invalid-feedback" id="second_mobile_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="cnic" class="form-label fw-medium">CNIC No <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-card-text"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="cnic" name="cnic" placeholder="Enter CNIC number">
                        </div>
                        <div class="invalid-feedback" id="cnic-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="vehicle_no" class="form-label fw-medium">Vehicle No <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-truck"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="vehicle_no" name="vehicle_no" placeholder="Enter vehicle number">
                        </div>
                        <div class="invalid-feedback" id="vehicle_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="city" class="form-label fw-medium">City <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="city" name="city" placeholder="Enter city">
                        </div>
                        <div class="invalid-feedback" id="city-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="address" class="form-label fw-medium">Address</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-house"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="address" name="address" rows="1" placeholder="Enter address"></textarea>
                        </div>
                        <div class="invalid-feedback" id="address-error"></div>
                    </div>

                    <div class="col-md-12">
                        <label for="reference" class="form-label fw-medium">Reference</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-journal-text"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="reference" name="reference" rows="2" placeholder="Enter reference details"></textarea>
                        </div>
                        <div class="invalid-feedback" id="reference-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveBtn" class="btn btn-primary">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1"></i>Save Driver
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Driver Modal -->
<div class="modal fade" id="editDriverModal" tabindex="-1" aria-labelledby="editDriverModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="editDriverModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Driver
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editDriverForm" class="row g-3" action="{{ route('admin.drivers.update') }}" method="POST">
                    @csrf
                    <input type="hidden" id="edit_driver_id" name="id">

                    <div class="col-md-6">
                        <label for="edit_driver_type" class="form-label fw-medium">Driver Type <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_driver_type" name="driver_type" placeholder="Enter driver type">
                        </div>
                        <div class="invalid-feedback" id="edit_driver_type-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_driver_name" class="form-label fw-medium">Driver Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_driver_name" name="driver_name" placeholder="Enter driver name">
                        </div>
                        <div class="invalid-feedback" id="edit_driver_name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_first_mobile_no" class="form-label fw-medium">Mobile No. <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_first_mobile_no" name="first_mobile_no" placeholder="Enter primary mobile number">
                        </div>
                        <div class="invalid-feedback" id="edit_first_mobile_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_second_mobile_no" class="form-label fw-medium">Mobile No.2</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_second_mobile_no" name="second_mobile_no" placeholder="Enter secondary mobile number">
                        </div>
                        <div class="invalid-feedback" id="edit_second_mobile_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_cnic" class="form-label fw-medium">CNIC No <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-card-text"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_cnic" name="cnic" placeholder="Enter CNIC number">
                        </div>
                        <div class="invalid-feedback" id="edit_cnic-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_vehicle_no" class="form-label fw-medium">Vehicle No <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-truck"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_vehicle_no" name="vehicle_no" placeholder="Enter vehicle number">
                        </div>
                        <div class="invalid-feedback" id="edit_vehicle_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_city" class="form-label fw-medium">City <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_city" name="city" placeholder="Enter city">
                        </div>
                        <div class="invalid-feedback" id="edit_city-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_address" class="form-label fw-medium">Address</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-house"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="edit_address" name="address" rows="1" placeholder="Enter address"></textarea>
                        </div>
                        <div class="invalid-feedback" id="edit_address-error"></div>
                    </div>

                    <div class="col-md-12">
                        <label for="edit_reference" class="form-label fw-medium">Reference</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-journal-text"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="edit_reference" name="reference" rows="2" placeholder="Enter reference details"></textarea>
                        </div>
                        <div class="invalid-feedback" id="edit_reference-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="updateBtn" class="btn btn-primary">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-check-circle me-1"></i>Update Driver
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
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Driver
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Are you sure?</h5>
                <p class="text-muted mb-0">You won't be able to revert this action!</p>
                <input type="hidden" id="delete_driver_id">
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
<script src="{{ asset('js/driver-ajax.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#driversTable').DataTable({
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
