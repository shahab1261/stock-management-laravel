@extends('admin.layout.master')

@section('title', 'Company Settings')
@section('description', 'Manage company information and settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-gear text-primary me-2"></i>Company Settings</h3>
            <p class="text-muted mb-0">Manage your company information and system settings</p>
        </div>
    </div>

    <!-- Settings Form Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Company Information</h5>
                </div>
                <div class="card-body p-4">
                    <form id="settingsForm" action="{{ route('admin.management.settings.update') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                        @csrf

                        <!-- Company Logo -->
                        <div class="col-md-12 mb-4">
                            <label for="logo" class="form-label fw-medium">Company Logo</label>
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar-lg border rounded p-2 bg-light">
                                        @if(isset($settings) && $settings->logo_path)
                                            <img src="{{ asset($settings->logo_path) }}" alt="Company Logo" class="img-fluid" style="max-height: 100px; max-width: 100%;">
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="bi bi-building fs-1"></i>
                                                <p class="small mb-0">No logo</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <input type="file" class="form-control" id="logo" name="logo_path" accept="image/*">
                                        <label class="input-group-text" for="logo"><i class="bi bi-upload"></i></label>
                                    </div>
                                    <div class="form-text">Recommended size: 200x80 pixels. Max file size: 2MB.</div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Company Name -->
                        <div class="col-md-6">
                            <label for="company_name" class="form-label fw-medium">Company Name <span class="text-danger">*</span></label>
                            <div class="input-group mb-0">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-building"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="company_name" name="company_name"
                                    placeholder="Enter company name" value="{{ $settings->company_name ?? '' }}" required>
                            </div>
                            <div class="invalid-feedback" id="company_name-error"></div>
                        </div>

                        <!-- Short Description -->
                        <div class="col-md-6">
                            <label for="short_desc" class="form-label fw-medium">Short Description</label>
                            <div class="input-group mb-0">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-card-text"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="short_desc" name="short_desc"
                                    placeholder="Enter short description" value="{{ $settings->short_desc ?? '' }}">
                            </div>
                            <div class="invalid-feedback" id="short_desc-error"></div>
                        </div>

                        <!-- Date Lock -->
                        <div class="col-md-6">
                            <label for="date_lock" class="form-label fw-medium">Date Lock <span class="text-danger">*</span></label>
                            <div class="input-group mb-0">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="date" class="form-control border-start-0" id="date_lock" name="date_lock"
                                    value="{{ $settings->date_lock ?? '' }}" required>
                            </div>
                            <div class="form-text">Entries before this date cannot be modified</div>
                            <div class="invalid-feedback" id="date_lock-error"></div>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4" id="updateSettingsBtn">
                                <i class="bi bi-save me-2"></i>Update Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Submit form via AJAX
        $('#settingsForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#updateSettingsBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...').attr('disabled', true);
                    $('.is-invalid').removeClass('is-invalid');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    $('#updateSettingsBtn').html('<i class="bi bi-save me-2"></i>Update Settings').attr('disabled', false);

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '-error').text(value[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                complete: function() {
                    $('#updateSettingsBtn').html('<i class="bi bi-save me-2"></i>Update Settings').attr('disabled', false);
                }
            });
        });

        // Preview uploaded image
        $('#logo').change(function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.avatar-lg').html('<img src="' + e.target.result + '" alt="Company Logo" class="img-fluid" style="max-height: 100px; max-width: 100%;">');
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
