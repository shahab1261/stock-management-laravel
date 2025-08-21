@extends('admin.layout.master')

@section('title', 'Manage Tanks')
@section('description', 'Manage your tank configurations')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-database text-primary me-2"></i>Tanks</h3>
            <p class="text-muted mb-0">Manage your tank configurations and storage</p>
        </div>
    </div>

    <!-- Tanks Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Tanks</h5>
                    <button type="button" id="addNewTankBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Tank
                    </button>
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table id="tanksTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="ps-3">ID</th>
                                    <th width="10%" class="ps-3">Name</th>
                                    <th width="10%" class="ps-3">Limit</th>
                                    <th width="10%" class="ps-3">Opening Stock</th>
                                    <th width="10%" class="ps-3">Cost Price</th>
                                    <th width="10%" class="ps-3">Sales Price</th>
                                    <th width="10%" class="ps-3">Product</th>
                                    <th width="10%" class="ps-3">Date</th>
                                    <th width="10%" class="ps-3">Notes</th>
                                    <th width="15%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tanks as $tank)
                                <tr>
                                    <td class="ps-3">{{ $tank->id }}</td>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($tank->tank_name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $tank->tank_name }}</span>
                                        </div>
                                    </td>
                                    <td class="ps-3">{{ $tank->tank_limit }}</td>
                                    <td class="ps-3">{{ $tank->opening_stock }}</td>
                                    <td class="ps-3">{{ $tank->cost_price }}</td>
                                    <td class="ps-3">{{ $tank->sales_price }}</td>
                                    <td class="ps-3">{{ $tank->product ? $tank->product->name : 'N/A' }}</td>
                                    <td class="ps-3">{{ date('d M Y', strtotime($tank->ob_date)) }}</td>
                                    <td class="ps-3">{{ Str::limit($tank->notes, 20) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary view-dip-charts me-1"
                                            data-id="{{ $tank->id }}"
                                            data-name="{{ $tank->tank_name }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-tank me-1"
                                            data-id="{{ $tank->id }}"
                                            data-tank_name="{{ $tank->tank_name }}"
                                            data-tank_limit="{{ $tank->tank_limit }}"
                                            data-opening_stock="{{ $tank->opening_stock }}"
                                            data-is_dippable="{{ $tank->is_dippable }}"
                                            data-cost_price="{{ $tank->cost_price }}"
                                            data-sales_price="{{ $tank->sales_price }}"
                                            data-ob_date="{{ $tank->ob_date }}"
                                            data-product_id="{{ $tank->product_id }}"
                                            data-notes="{{ $tank->notes }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-tank" data-id="{{ $tank->id }}">
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

<!-- Add Tank Modal -->
<div class="modal fade" id="addTankModal" tabindex="-1" aria-labelledby="addTankModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="addTankModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Tank
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addTankForm" action="{{ route('admin.tanks.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="tank_name" class="form-label fw-medium">Tank Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-database"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="tank_name" name="tank_name" placeholder="Enter tank name">
                        </div>
                        <div class="invalid-feedback" id="tank_name-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="product_id" class="form-label fw-medium">Product <span class="text-danger">*</span></label>
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
                        <label for="tank_limit" class="form-label fw-medium">Tank Limit <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-arrow-up-circle"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="tank_limit" name="tank_limit" placeholder="Enter tank limit">
                        </div>
                        <div class="invalid-feedback" id="tank_limit-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="opening_stock" class="form-label fw-medium">Stock of product <small>(stock can only be added thru purchase)</small></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-box"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="opening_stock" name="opening_stock" value="0" placeholder="Enter opening stock" readonly>
                        </div>
                        <div class="invalid-feedback" id="opening_stock-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="cost_price" class="form-label fw-medium">Cost Price <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-currency-dollar"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="cost_price" name="cost_price" placeholder="Enter cost price">
                        </div>
                        <div class="invalid-feedback" id="cost_price-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="sales_price" class="form-label fw-medium">Sales Price <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-cash"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="sales_price" name="sales_price" placeholder="Enter sales price">
                        </div>
                        <div class="invalid-feedback" id="sales_price-error"></div>
                    </div>
                    {{-- <div class="col-md-6">
                        <label for="ob_date" class="form-label fw-medium">Opening Balance Date <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-calendar"></i>
                            </span>
                            <input type="date" class="form-control border-start-0" id="ob_date" name="ob_date">
                        </div>
                        <div class="invalid-feedback" id="ob_date-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="is_dippable" class="form-label fw-medium">Is Dippable <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-toggles"></i>
                            </span>
                            <select class="form-select border-start-0" id="is_dippable" name="is_dippable" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="invalid-feedback" id="is_dippable-error"></div>
                    </div> --}}
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
                <button type="submit" form="addTankForm" class="btn btn-primary add-tank">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Save Tank
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Tank Modal -->
<div class="modal fade" id="editTankModal" tabindex="-1" aria-labelledby="editTankModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="editTankModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Tank
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editTankForm" action="{{ route('admin.tanks.update') }}" method="POST" class="row g-3">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="col-md-6">
                        <label for="edit_tank_name" class="form-label fw-medium">Tank Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-database"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_tank_name" name="tank_name" placeholder="Enter tank name">
                        </div>
                        <div class="invalid-feedback" id="edit-tank_name-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_product_id" class="form-label fw-medium">Product <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-droplet"></i>
                            </span>
                            <select class="form-select border-start-0" id="edit_product_id" name="product_id" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="edit-product_id-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_tank_limit" class="form-label fw-medium">Tank Limit <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-arrow-up-circle"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="edit_tank_limit" name="tank_limit" placeholder="Enter tank limit">
                        </div>
                        <div class="invalid-feedback" id="edit-tank_limit-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_opening_stock" class="form-label fw-medium">Opening Stock <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-box"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="edit_opening_stock" name="opening_stock" placeholder="Enter opening stock">
                        </div>
                        <div class="invalid-feedback" id="edit-opening_stock-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_cost_price" class="form-label fw-medium">Cost Price <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-currency-dollar"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="edit_cost_price" name="cost_price" placeholder="Enter cost price">
                        </div>
                        <div class="invalid-feedback" id="edit-cost_price-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_sales_price" class="form-label fw-medium">Sales Price <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-cash"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="edit_sales_price" name="sales_price" placeholder="Enter sales price">
                        </div>
                        <div class="invalid-feedback" id="edit-sales_price-error"></div>
                    </div>
                    {{-- <div class="col-md-6">
                        <label for="edit_ob_date" class="form-label fw-medium">Opening Balance Date <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-calendar"></i>
                            </span>
                            <input type="date" class="form-control border-start-0" id="edit_ob_date" name="ob_date">
                        </div>
                        <div class="invalid-feedback" id="edit-ob_date-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_is_dippable" class="form-label fw-medium">Is Dippable <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-toggles"></i>
                            </span>
                            <select class="form-select border-start-0" id="edit_is_dippable" name="is_dippable" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="invalid-feedback" id="edit-is_dippable-error"></div>
                    </div> --}}
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
                <button type="submit" form="editTankForm" class="btn btn-primary edit-tank-btn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Update Tank
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteTankModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Tank
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
<script src="{{ asset('js/tank-ajax.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#tanksTable').DataTable({
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
