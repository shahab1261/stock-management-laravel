@extends('admin.layout.master')

@section('title', 'Manage Suppliers')
@section('description', 'Manage your suppliers')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-people text-primary me-2"></i>Suppliers</h3>
            <p class="text-muted mb-0">Manage your suppliers information</p>
        </div>
    </div>

    <!-- Supplier Cards Overview -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary bg-opacity-10 p-3 me-3" style="width: 66px; height: 66px;">
                        <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Suppliers</h6>
                        <h3 class="mb-0">{{ count($suppliers) }}</h3>
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
                        <h6 class="text-muted mb-1">Active Suppliers</h6>
                        <h3 class="mb-0">{{ $suppliers->where('status', 1)->count() }}</h3>
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
                        <h6 class="text-muted mb-1">Inactive Suppliers</h6>
                        <h3 class="mb-0">{{ $suppliers->where('status', 2)->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suppliers Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Suppliers</h5>
                    <button type="button" id="addNewSupplierBtn" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i> Add New Supplier
                    </button>
                </div>
                <div class="card-body p-0 pt-0">
                    <div class="table-responsive">
                        <table id="suppliersTable" class="table table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th width="3%" class="ps-3">#</th>
                                    <th width="10%" class="ps-3">Name</th>
                                    <th width="8%" class="ps-3">Contact Person</th>
                                    <th width="8%" class="ps-3">Email</th>
                                    <th width="7%" class="ps-3">Phone Number</th>
                                    <th width="7%" class="ps-3">Supplier Type</th>
                                    <th width="7%" class="ps-3">NTN No</th>
                                    <th width="7%" class="ps-3">GST No</th>
                                    <th width="7%" class="ps-3">Fax No</th>
                                    <th width="8%" class="ps-3">Balance</th>
                                    <th width="8%" class="ps-3">Item Type</th>
                                    <th width="6%" class="ps-3">Address</th>
                                    <th width="6%" class="ps-3">Terms & Conditions</th>
                                    <th width="5%" class="ps-3">Status</th>
                                    <th width="9%" class="ps-3">Registered Date</th>
                                    <th width="10%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $key => $supplier)
                                <tr>
                                    <td class="ps-3">{{ $key + 1 }}</td>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary">{{ substr($supplier->name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $supplier->name }}</span>
                                        </div>
                                    </td>
                                    <td class="ps-3">{{ $supplier->contact_person }}</td>
                                    <td class="ps-3">{{ $supplier->email }}</td>
                                    <td class="ps-3">{{ $supplier->phone }}</td>
                                    <td class="ps-3">{{ $supplier->supplier_type }}</td>
                                    <td class="ps-3">{{ $supplier->ntn_no }}</td>
                                    <td class="ps-3">{{ $supplier->gst_no }}</td>
                                    <td class="ps-3">{{ $supplier->fax_no }}</td>
                                    <td class="ps-3">
                                        <span class="badge bg-success">Rs {{ number_format($supplier->opening_balance, 2) }}</span>
                                    </td>
                                    <td class="ps-3">{{ $supplier->item_type }}</td>
                                    <td class="ps-3">{{ $supplier->address }}</td>
                                    <td class="ps-3">{{ $supplier->terms }}</td>
                                    <td class="ps-3">
                                        @if($supplier->status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Non Active</span>
                                        @endif
                                    </td>
                                    <td class="ps-3">{{ date('d-m-Y', strtotime($supplier->created_at)) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-supplier me-1"
                                            data-id="{{ $supplier->id }}"
                                            data-name="{{ $supplier->name }}"
                                            data-supplier-type="{{ $supplier->supplier_type }}"
                                            data-contact-person="{{ $supplier->contact_person }}"
                                            data-item-type="{{ $supplier->item_type }}"
                                            data-mobile="{{ $supplier->phone }}"
                                            data-email="{{ $supplier->email }}"
                                            data-fax="{{ $supplier->fax_no }}"
                                            data-ntn="{{ $supplier->ntn_no }}"
                                            data-gst="{{ $supplier->gst_no }}"
                                            data-balance="{{ $supplier->opening_balance }}"
                                            data-status="{{ $supplier->status }}"
                                            data-address="{{ $supplier->address }}"
                                            data-terms="{{ $supplier->terms }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-supplier" data-id="{{ $supplier->id }}">
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

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="addSupplierModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Supplier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addSupplierForm" action="{{ route('admin.suppliers.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-medium">Supplier Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-circle"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="name" name="name" placeholder="Enter supplier name" required>
                        </div>
                        <div class="invalid-feedback" id="name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="supplier_type" class="form-label fw-medium">Supplier Type <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="supplier_type" name="supplier_type" placeholder="Enter supplier type" required>
                        </div>
                        <div class="invalid-feedback" id="supplier_type-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="contact_person" class="form-label fw-medium">Contact Person <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="contact_person" name="contact_person" placeholder="Enter contact person name" required>
                        </div>
                        <div class="invalid-feedback" id="contact_person-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="item_type" class="form-label fw-medium">Type of Item <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-box"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="item_type" name="item_type" placeholder="Enter item type" required>
                        </div>
                        <div class="invalid-feedback" id="item_type-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="mobile_no" class="form-label fw-medium">Mobile No. <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="mobile_no" name="mobile_no" placeholder="Enter mobile number" required>
                        </div>
                        <div class="invalid-feedback" id="mobile_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="Enter email address" required>
                        </div>
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="fax_no" class="form-label fw-medium">Fax No.</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-printer"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="fax_no" name="fax_no" placeholder="Enter fax number">
                        </div>
                        <div class="invalid-feedback" id="fax_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="ntn_no" class="form-label fw-medium">NTN No</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-file-text"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="ntn_no" name="ntn_no" placeholder="Enter NTN number">
                        </div>
                        <div class="invalid-feedback" id="ntn_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="gst_no" class="form-label fw-medium">GST No</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-receipt"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="gst_no" name="gst_no" placeholder="Enter GST number">
                        </div>
                        <div class="invalid-feedback" id="gst_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="balance" class="form-label fw-medium">Balance</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-cash"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="balance" name="balance" placeholder="Enter balance amount">
                        </div>
                        <div class="invalid-feedback" id="balance-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-toggle-on"></i>
                            </span>
                            <select class="form-select border-start-0" id="status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Non Active</option>
                            </select>
                        </div>
                        <div class="invalid-feedback" id="status-error"></div>
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
                        <label for="terms" class="form-label fw-medium">Terms & Conditions</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-file-earmark-text"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="terms" name="terms" rows="2" placeholder="Enter terms and conditions"></textarea>
                        </div>
                        <div class="invalid-feedback" id="terms-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addSupplierForm" class="btn btn-primary add_supplier">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Add Supplier
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary bg-opacity-10 border-0">
                <h5 class="modal-title" id="editSupplierModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Supplier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editSupplierForm" action="{{ route('admin.suppliers.update') }}" class="row g-3">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="col-md-6">
                        <label for="edit_name" class="form-label fw-medium">Supplier Name <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-circle"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_name" name="name" placeholder="Enter supplier name" required>
                        </div>
                        <div class="invalid-feedback" id="edit-name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_supplier_type" class="form-label fw-medium">Supplier Type <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-tag"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_supplier_type" name="supplier_type" placeholder="Enter supplier type" required>
                        </div>
                        <div class="invalid-feedback" id="edit-supplier_type-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_contact_person" class="form-label fw-medium">Contact Person <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_contact_person" name="contact_person" placeholder="Enter contact person name" required>
                        </div>
                        <div class="invalid-feedback" id="edit-contact_person-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_item_type" class="form-label fw-medium">Type of Item <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-box"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_item_type" name="item_type" placeholder="Enter item type" required>
                        </div>
                        <div class="invalid-feedback" id="edit-item_type-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_mobile_no" class="form-label fw-medium">Mobile No. <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_mobile_no" name="mobile_no" placeholder="Enter mobile number" required>
                        </div>
                        <div class="invalid-feedback" id="edit-mobile_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control border-start-0" id="edit_email" name="email" placeholder="Enter email address" required>
                        </div>
                        <div class="invalid-feedback" id="edit-email-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_fax_no" class="form-label fw-medium">Fax No.</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-printer"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_fax_no" name="fax_no" placeholder="Enter fax number">
                        </div>
                        <div class="invalid-feedback" id="edit-fax_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_ntn_no" class="form-label fw-medium">NTN No</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-file-text"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_ntn_no" name="ntn_no" placeholder="Enter NTN number">
                        </div>
                        <div class="invalid-feedback" id="edit-ntn_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_gst_no" class="form-label fw-medium">GST No</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-receipt"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="edit_gst_no" name="gst_no" placeholder="Enter GST number">
                        </div>
                        <div class="invalid-feedback" id="edit-gst_no-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_balance" class="form-label fw-medium">Balance</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-cash"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-start-0" id="edit_balance" name="balance" placeholder="Enter balance amount">
                        </div>
                        <div class="invalid-feedback" id="edit-balance-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="edit_status" class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-toggle-on"></i>
                            </span>
                            <select class="form-select border-start-0" id="edit_status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Non Active</option>
                            </select>
                        </div>
                        <div class="invalid-feedback" id="edit-status-error"></div>
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
                        <label for="edit_terms" class="form-label fw-medium">Terms & Conditions</label>
                        <div class="input-group mb-0">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-file-earmark-text"></i>
                            </span>
                            <textarea class="form-control border-start-0" id="edit_terms" name="terms" rows="2" placeholder="Enter terms and conditions"></textarea>
                        </div>
                        <div class="invalid-feedback" id="edit-terms-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editSupplierForm" class="btn btn-primary edit_supplier">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                    <i class="bi bi-save me-1 submit-icon"></i>Update Supplier
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSupplierModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="width: 400px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Supplier
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
<script src="{{ asset('js/suppliers-ajax.js') }}"></script>
@endpush
