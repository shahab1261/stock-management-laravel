@extends('admin.layout.master')

@section('title', 'Edit Credit Sale Vendor')
@section('description', 'Update vendor account for this credit sale')

@section('content')
@permission('sales.credit.edit')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-person-gear text-primary me-2"></i>Edit Vendor</h3>
            <p class="text-muted mb-0">Update only the vendor account for this credit sale</p>
        </div>
    </div>

    <form id="editVendorForm" action="{{ route('sales.credit.update-vendor', $creditSale->id) }}" method="POST">
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
                                    <option value="{{ $supplier->id }}" data-type="1" {{ $creditSale->vendor_type == 1 && $creditSale->vendor_id == $supplier->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $supplier->name) }} (Supplier)</option>
                                @endforeach
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" data-type="2" {{ $creditSale->vendor_type == 2 && $creditSale->vendor_id == $customer->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $customer->name) }} (Customer)</option>
                                @endforeach
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-type="3" {{ $creditSale->vendor_type == 3 && $creditSale->vendor_id == $product->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $product->name) }} (Product)</option>
                                @endforeach
                                @foreach ($expenses as $expense)
                                    <option value="{{ $expense->id }}" data-type="4" {{ $creditSale->vendor_type == 4 && $creditSale->vendor_id == $expense->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $expense->expense_name) }} (Expense)</option>
                                @endforeach
                                @foreach ($incomes as $income)
                                    <option value="{{ $income->id }}" data-type="5" {{ $creditSale->vendor_type == 5 && $creditSale->vendor_id == $income->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $income->income_name) }} (Income)</option>
                                @endforeach
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" data-type="6" {{ $creditSale->vendor_type == 6 && $creditSale->vendor_id == $bank->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $bank->name) }} (Bank)</option>
                                @endforeach
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" data-type="9" {{ $creditSale->vendor_type == 9 && $creditSale->vendor_id == $employee->id ? 'selected' : '' }}>{{ str_replace('&amp;', '&', $employee->name) }} (Employee)</option>
                                @endforeach
                                <option value="7" data-type="7" {{ $creditSale->vendor_type == 7 && $creditSale->vendor_id == 7 ? 'selected' : '' }}>Cash</option>
                                <option value="8" data-type="8" {{ $creditSale->vendor_type == 8 && $creditSale->vendor_id == 8 ? 'selected' : '' }}>Mp</option>
                            </select>
                            <input type="hidden" name="vendor_data_type" id="vendor_data_type" value="{{ $creditSale->vendor_type }}">
                        </div>
                    </div>

                    <div class="col-md-6" style="margin-top: -8px !important;">
                        <label class="form-label fw-medium text-muted mb-2">Select Vehicle</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-truck"></i>
                            </span>
                            <select class="form-select border-start-0 searchable-dropdown" name="vehicle_id" id="vehicle_id" required>
                                <option value="" selected disabled>Choose vehicle</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mb-4">
            <a href="{{ route('sales.credit.index') }}" class="btn btn-outline-secondary px-4">
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
    $(function(){
        const $vendorSelect = $('#vendor');
        const $vendorTypeInput = $('#vendor_data_type');
        const $vehicleSelect = $('#vehicle_id');
        const initialVehicleId = "{{ $creditSale->vehicle_id }}";

        // Function to load vehicles via AJAX
        function loadVehicles(vendorId, vendorType, selectedVehicleId = null) {
            if (!$vehicleSelect.length) return;

            // Clear current options and show loading
            $vehicleSelect.html('<option value="" selected disabled>Loading...</option>').trigger('change');

            // Only Customers (2) and Suppliers (1) typically have vehicles
            if (vendorType != 2 && vendorType != 1) {
                $vehicleSelect.html('<option value="" selected disabled>No vehicles for this type</option>').trigger('change');
                return;
            }

            $.ajax({
                url: '{{ route('sales.credit.customer_vehicles') }}',
                type: 'POST',
                data: {
                    customer_id: vendorId,
                    vendor_type: vendorType,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.success && data.vehicles && data.vehicles.length > 0) {
                        let options = '<option value="" disabled>Choose vehicle</option>';
                        data.vehicles.forEach(vehicle => {
                            const selected = (selectedVehicleId && vehicle.id == selectedVehicleId) ? 'selected' : '';
                            options += `<option value="${vehicle.id}" ${selected}>${vehicle.larry_name}</option>`;
                        });
                        $vehicleSelect.html(options).trigger('change');
                    } else {
                        $vehicleSelect.html('<option value="" selected disabled>No vehicles found</option>').trigger('change');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading vehicles:', xhr);
                    $vehicleSelect.html('<option value="" selected disabled>Error loading vehicles</option>').trigger('change');
                }
            });
        }

        // Vendor selection change event
        $vendorSelect.on('change', function() {
            const $selectedOption = $(this).find('option:selected');
            const type = $selectedOption.attr('data-type');
            const vendorId = $(this).val();
            
            if ($vendorTypeInput.length) {
                $vendorTypeInput.val(type);
            }

            // Load vehicles when vendor changes
            loadVehicles(vendorId, type);
        });

        // Initial load
        const $initialSelected = $vendorSelect.find('option:selected');
        if ($initialSelected.length) {
            const type = $initialSelected.attr('data-type');
            const vendorId = $vendorSelect.val();
            loadVehicles(vendorId, type, initialVehicleId);
        }

        const $form = $('#editVendorForm');
        const $saveBtn = $('#saveVendorBtn');

        if ($form.length) {
            $form.on('submit', function(e){
                e.preventDefault();
                
                const url = $(this).attr('action');
                const formData = new FormData(this);

                if ($saveBtn.length) {
                    $saveBtn.prop('disabled', true);
                    $saveBtn.data('originalHtml', $saveBtn.html());
                    $saveBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...');
                }

                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            toast.fire({ icon: 'success', title: data.message || 'Vendor updated successfully' });
                            setTimeout(function(){
                                window.location.href = data.redirect || '{{ route('sales.credit.index') }}';
                            }, 900);
                        } else {
                            toast.fire({ icon: 'error', title: data.message || 'Failed to update vendor' });
                            $saveBtn.prop('disabled', false).html($saveBtn.data('originalHtml'));
                        }
                    },
                    error: function(xhr) {
                        const data = xhr.responseJSON;
                        const message = (data && data.message) ? data.message : 'Unexpected error';
                        toast.fire({ icon: 'error', title: message });
                        $saveBtn.prop('disabled', false).html($saveBtn.data('originalHtml'));
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection
