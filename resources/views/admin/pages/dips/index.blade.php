@extends('admin.layout.master')

@section('title', 'Dips Management')
@section('description', 'Manage tank dip readings and stock levels')

@push('styles')
<link href="{{ asset('css/dips.css') }}" rel="stylesheet">
@endpush

@section('content')
@permission('dips.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-columns-gap text-primary me-2"></i>Dips Management</h3>
            <p class="text-muted mb-0">Record and manage tank dip readings for {{ date('d M Y', strtotime($dateLock)) }}</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-list-ol text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Dips</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ $totalDips }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-success bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-droplet text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Liters</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ number_format($totalLiters) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-info bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-check-circle text-info" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Tanks with Dips</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ $tanksWithDips }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-warning bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Pending Tanks</h6>
                        <h3 class="mb-0" style="font-size: 1.7rem;">{{ $tanksWithoutDips }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dips Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Dip Readings</h5>
                    <button type="button" id="addDipBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Dip
                    </button>
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0" id="dipsTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50" class="ps-3 text-center">#</th>
                                    <th class="ps-3 text-center">Dip Date</th>
                                    <th class="ps-3 text-center">Tank Name</th>
                                    <th class="ps-3 text-center">Product Name</th>
                                    <th class="ps-3 text-center">Dip (mm)</th>
                                    <th class="ps-3 text-center">Liters</th>
                                    <th width="120" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dips as $key => $dip)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">{{ $dip->dip_date->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($dip->tank->tank_name ?? 'N/A', 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $dip->tank->tank_name ?? 'Not Found' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-success">{{ substr($dip->product->name ?? 'N/A', 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $dip->product->name ?? 'Not Found' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ number_format($dip->dip_value) }} mm</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success">{{ number_format($dip->liters) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger delete-btn p-2"
                                                data-id="{{ $dip->id }}"
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

<!-- Add Dip Modal -->
<div class="modal fade" id="dipModal" tabindex="-1" aria-labelledby="dipModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" id="modalBody">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Add New Dip Reading
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="dipEntryForm" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label for="tank_list" class="form-label fw-medium">Select Tank <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-database"></i>
                            </span>
                            <select id="tank_list" name="tank_id" class="form-select border-start-0 searchable-dropdown-modal" required>
                                <option selected disabled>Select Tank</option>
                                @foreach($tanks as $tank)
                                    <option value="{{ $tank->id }}" data-productid="{{ $tank->product_id }}">
                                        {{ $tank->tank_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="tank_list-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="dip_date" class="form-label fw-medium">Dip Date <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-calendar"></i>
                            </span>
                            <input type="date" class="form-control border-start-0" id="dip_date" name="dip_date"
                                   value="{{ $dateLock }}" max="{{ date('Y-m-d') }}" readonly required>
                        </div>
                        <div class="invalid-feedback" id="dip_date-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="dip_value" class="form-label fw-medium">Dip (mm) <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-rulers"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="dip_value"
                                   name="dip_value" min="0" required>
                        </div>
                        <div class="invalid-feedback" id="dip_value-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="liter_value" class="form-label fw-medium">Liters <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-droplet"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="liter_value"
                                   name="liter_value" min="0" readonly required>
                        </div>
                        <div class="invalid-feedback" id="liter_value-error"></div>
                    </div>

                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Liters will be automatically calculated based on the dip value and tank's dip chart.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" style="background-color: #fdfdfd;" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="submit" id="dipBtn" class="btn btn-primary px-4" form="dipEntryForm">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1"></i> <span id="dipBtnText">Submit</span>
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
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Dip Reading
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Are you sure?</h5>
                <p class="text-muted mb-0">You won't be able to revert this action!</p>
                <input type="hidden" id="delete_dip_id">
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
@endpermission
@endsection

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

<!-- Hidden routes data for JavaScript -->
<script type="text/javascript">
    window.routes = {
        store: '{{ route("admin.dips.store") }}',
        delete: '{{ route("admin.dips.delete") }}',
        getLiters: '{{ route("admin.dips.get-liters") }}',
        getTankProduct: '{{ route("admin.dips.get-tank-product") }}'
    };
</script>

@push('scripts')
<script src="{{ asset('js/dips.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#dipsTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 25,
            order: [[0, 'asc']],
        });

        document.title = "Dips Management";
    });
</script>
@endpush
