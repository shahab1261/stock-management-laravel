@extends('admin.layout.master')

@section('title', 'Edit Journal Entry Vendor')
@section('description', 'Update vendor account for this journal entry')

@section('content')
@permission('journal.edit')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-person-gear text-primary me-2"></i>Edit Vendor</h3>
            <p class="text-muted mb-0">Update only the vendor account for this journal entry</p>
        </div>
    </div>

    <form id="editVendorForm" action="{{ route('journal.update-vendor', $journalEntry->id) }}" method="POST">
        @csrf

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shop text-primary fs-4 me-2"></i>
                    <h2 class="fs-5 fw-bold mb-0">Vendor</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6" style="margin-top: -8px !important;">
                        <label class="form-label fw-medium text-muted mb-2">Select Vendor</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-shop"></i>
                            </span>
                            <select class="form-select border-start-0 searchable-dropdown" name="vendor_id" id="vendor" required>
                                <option disabled>Select Vendor</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" data-type="1" {{ $journalEntry->vendor_type == 1 && $journalEntry->vendor_id == $supplier->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $supplier->name) }} (Supplier)</option>
                                @endforeach
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" data-type="2" {{ $journalEntry->vendor_type == 2 && $journalEntry->vendor_id == $customer->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $customer->name) }} (Customer)</option>
                                @endforeach
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-type="3" {{ $journalEntry->vendor_type == 3 && $journalEntry->vendor_id == $product->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $product->name) }} (Product)</option>
                                @endforeach
                                @foreach ($expenses as $expense)
                                    <option value="{{ $expense->id }}" data-type="4" {{ $journalEntry->vendor_type == 4 && $journalEntry->vendor_id == $expense->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $expense->expense_name) }} (Expense)</option>
                                @endforeach
                                @foreach ($incomes as $income)
                                    <option value="{{ $income->id }}" data-type="5" {{ $journalEntry->vendor_type == 5 && $journalEntry->vendor_id == $income->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $income->income_name) }} (Income)</option>
                                @endforeach
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" data-type="6" {{ $journalEntry->vendor_type == 6 && $journalEntry->vendor_id == $bank->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $bank->name) }} (Bank)</option>
                                @endforeach
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" data-type="9" {{ $journalEntry->vendor_type == 9 && $journalEntry->vendor_id == $employee->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $employee->name) }} (Employee)</option>
                                @endforeach
                                <option value="7" data-type="7" {{ $journalEntry->vendor_type == 7 && $journalEntry->vendor_id == 7 ? 'selected' : '' }}>Cash</option>
                                <option value="8" data-type="8" {{ $journalEntry->vendor_type == 8 && $journalEntry->vendor_id == 8 ? 'selected' : '' }}>Mp</option>
                            </select>
                            <input type="hidden" name="vendor_data_type" id="vendor_data_type" value="{{ $journalEntry->vendor_type }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mb-4">
            <a href="{{ route('admin.journal.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-x-circle me-1"></i>Cancel
            </a>
            <button type="submit" id="saveVendorBtn" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i>Update Vendor
            </button>
        </div>
    </form>
</div>
@endpermission

@push('scripts')
<script src="{{ asset('assets/sweetalert2/sweetalert2.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/sweetalert2/sweetalert2.min.css') }}" />
<script>
    (function(){
        const vendorSelect = document.getElementById('vendor');
        const vendorTypeInput = document.getElementById('vendor_data_type');
        if (vendorSelect) {
            vendorSelect.addEventListener('change', function() {
                const selected = vendorSelect.options[vendorSelect.selectedIndex];
                const type = selected.getAttribute('data-type');
                vendorTypeInput.value = type;
            });
            // Ensure we capture existing selection's data-type on load
            const selected = vendorSelect.options[vendorSelect.selectedIndex];
            if (selected) {
                const type = selected.getAttribute('data-type');
                if (type) vendorTypeInput.value = type;
            }
        }

        const form = document.getElementById('editVendorForm');
        const saveBtn = document.getElementById('saveVendorBtn');
        if (form) {
            form.addEventListener('submit', async function(e){
                e.preventDefault();
                const url = form.getAttribute('action');
                const formData = new FormData(form);
                // Force-sync vendor_data_type from the currently selected option at submit time
                if (vendorSelect && vendorSelect.selectedIndex >= 0) {
                    const selected = vendorSelect.options[vendorSelect.selectedIndex];
                    const type = selected ? selected.getAttribute('data-type') : '';
                    if (type) {
                        formData.set('vendor_data_type', type);
                        if (vendorTypeInput) vendorTypeInput.value = type; // keep hidden input in sync
                    }
                }
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.dataset.originalHtml = saveBtn.innerHTML;
                    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                }

                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok || data.success === false) {
                        const message = (data && data.message) ? data.message : 'Failed to update vendor';
                        toast.fire({ icon: 'error', title: message });
                        return;
                    }

                    toast.fire({ icon: 'success', title: data.message || 'Vendor updated successfully' });
                    setTimeout(function(){
                        window.location.href = data.redirect || '{{ route('admin.journal.index') }}';
                    }, 900);
                } catch (err) {
                    toast.fire({ icon: 'error', title: (err && err.message) ? err.message : 'Unexpected error' });
                } finally {
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        if (saveBtn.dataset.originalHtml) {
                            saveBtn.innerHTML = saveBtn.dataset.originalHtml;
                        }
                    }
                }
            });
        }
    })();
</script>
@endpush
@endsection
