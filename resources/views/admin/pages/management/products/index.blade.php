@extends('admin.layout.master')

@section('title', 'Manage Products')
@section('description', 'Manage your product inventory')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-box text-primary me-2"></i>Products</h3>
            <p class="text-muted mb-0">Manage your product inventory and pricing</p>
        </div>
    </div>

    <!-- Product Cards Overview -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-box text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Products</h6>
                        <h3 class="mb-0">{{ count($products) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dippable Products -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-droplet text-success" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Dippable Products</h6>
                        <h3 class="mb-0">{{ $products->where('is_dippable', 1)->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Non Dippable Products -->
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-danger bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-droplet-half text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Non Dippable Products</h6>
                        <h3 class="mb-0">{{ $products->where('is_dippable', 0)->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Products</h5>
                    @permission('management.products.create')
                    <button type="button" id="addNewProductBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Product
                    </button>
                    @endpermission
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table id="productsTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="ps-3">#</th>
                                    <th width="15%" class="ps-3">Name</th>
                                    <th width="8%" class="ps-3">Tank/Associated</th>
                                    <th width="8%" class="ps-3">Unit</th>
                                    <th width="10%" class="ps-3">Current Purchase</th>
                                    <th width="10%" class="ps-3">Current Sale</th>
                                    <th width="8%" class="ps-3">Notes</th>
                                    <th width="10%" class="ps-3">Date of entry</th>
                                    <th width="15%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $index => $product)
                                <tr>
                                    <td class="ps-3">{{ $index + 1 }}</td>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($product->name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $product->name }}</span>
                                        </div>
                                    </td>
                                    <td class="ps-3">{{ $product->tank ? $product->tank->tank_name : 'Not Assigned' }}</td>
                                    <td class="ps-3">{{ $product->unit }}</td>
                                    <td class="ps-3">{{ $product->current_purchase }}</td>
                                    <td class="ps-3">{{ $product->current_sale }}</td>
                                    <td class="ps-3">{{ Str::limit($product->notes, 20) }}</td>
                                    <td class="ps-3">{{ date('d M Y', strtotime($product->created_at)) }}</td>
                                    <td class="text-center">
                                        @permission('management.products.edit')
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-product me-1"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-unit="{{ $product->unit }}"
                                            data-current_purchase="{{ $product->current_purchase }}"
                                            data-current_sale="{{ $product->current_sale }}"
                                            data-is_dippable="{{ $product->is_dippable }}"
                                            data-tank_id="{{ $product->tank_id }}"
                                            data-notes="{{ $product->notes }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        @endpermission
                                        @permission('management.products.delete')
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-product" data-id="{{ $product->id }}">
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="addProductModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Product
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addProductForm" action="{{ route('admin.products.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-box"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="name" name="name" placeholder="Enter product name">
                        </div>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="unit" class="form-label fw-medium">Unit <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-rulers"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="unit" name="unit" placeholder="Enter unit (e.g., Ltr, Kg)">
                        </div>
                        <div class="invalid-feedback" id="unit-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="current_purchase" class="form-label fw-medium">Current Purchase Price <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-currency-dollar"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="current_purchase" name="current_purchase" placeholder="Enter current purchase price">
                        </div>
                        <div class="invalid-feedback" id="current_purchase-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="current_sale" class="form-label fw-medium">Current Sale Price <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="current_sale" name="current_sale" placeholder="Enter current sale price">
                        </div>
                        <div class="invalid-feedback" id="current_sale-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="is_dippable" class="form-label fw-medium">Dippable Product <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-droplet"></i>
                            </span>
                            <select class="form-select border-start-0" id="is_dippable" name="is_dippable">
                                <option value="">Select Option</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="invalid-feedback" id="is_dippable-error"></div>
                    </div>
                    <div class="col-md-6 tank-field" style="display: none;">
                        <label for="tank_id" class="form-label fw-medium">Tank</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-database"></i>
                            </span>
                            <select class="form-select border-start-0" id="tank_id" name="tank_id">
                                <option value="">Select Tank</option>
                                @foreach($tanks as $tank)
                                    <option value="{{ $tank->id }}">{{ $tank->tank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="tank_id-error"></div>
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
                <button type="submit" form="addProductForm" class="btn btn-primary add-product">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Save Product
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="editProductModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Product
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editProductForm" action="{{ route('admin.products.update') }}" class="row g-3">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="col-md-6">
                        <label for="edit_name" class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-box"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_name" name="name" placeholder="Enter product name">
                        </div>
                        <div class="invalid-feedback" id="edit-name-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_unit" class="form-label fw-medium">Unit <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-rulers"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_unit" name="unit" placeholder="Enter unit (e.g., Ltr, Kg)">
                        </div>
                        <div class="invalid-feedback" id="edit-unit-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_current_purchase" class="form-label fw-medium">Current Purchase Price <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-currency-dollar"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="edit_current_purchase" name="current_purchase" placeholder="Enter current purchase price">
                        </div>
                        <div class="invalid-feedback" id="edit-current_purchase-error"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_current_sale" class="form-label fw-medium">Current Sale Price <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="edit_current_sale" name="current_sale" placeholder="Enter current sale price">
                        </div>
                        <div class="invalid-feedback" id="edit-current_sale-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editProductForm" class="btn btn-primary update-product">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Update Product
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Product
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="mb-3">Are you sure?</h5>
                <p class="text-muted mb-0">You won't be able to revert this action!</p>
                <input type="hidden" id="delete_product_id">
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
<script src="{{ asset('js/products-ajax.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#productsTable').DataTable({
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
