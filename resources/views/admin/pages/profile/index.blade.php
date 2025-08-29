@extends('admin.layout.master')

@section('title', 'My Profile')
@section('description', 'View and update your profile information')

@section('content')
@permission('profile.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-person-circle text-primary me-2"></i>My Profile</h3>
            <p class="text-muted mb-0">View and manage your account information</p>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card Section -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center pt-4 pb-3">
                    <div class="avatar-wrapper mb-3">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" class="avatar-lg rounded-circle border shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="avatar-lg rounded-circle bg-primary bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                <span class="text-primary fw-bold fs-1">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-semibold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                    <div class="d-flex justify-content-center mt-3">
                        <span class="badge bg-success py-2 px-3">Active</span>
                    </div>
                    <hr class="my-3">
                    <div class="contact-info text-start pb-2">
                        @if($user->phone)
                        <div class="d-flex align-items-center py-1">
                            <i class="bi bi-telephone text-primary me-3"></i>
                            <div>
                                <div class="small text-muted">Phone</div>
                                <div>{{ $user->phone }}</div>
                            </div>
                        </div>
                        @endif
                        @if($user->address)
                        <div class="d-flex align-items-center py-1">
                            <i class="bi bi-geo-alt text-primary me-3"></i>
                            <div>
                                <div class="small text-muted">Address</div>
                                <div>{{ $user->address }}</div>
                            </div>
                        </div>
                        @endif
                        @if($user->bank_account_number)
                        <div class="d-flex align-items-center py-1">
                            <i class="bi bi-bank text-primary me-3"></i>
                            <div>
                                <div class="small text-muted">Bank Account</div>
                                <div>{{ $user->bank_account_number }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="d-flex align-items-center py-1">
                            <i class="bi bi-calendar-check text-primary me-3"></i>
                            <div>
                                <div class="small text-muted">Joined Date</div>
                                <div>{{ date('d M Y', strtotime($user->created_at)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white p-0 border-0">
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active px-4 py-3" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                <i class="bi bi-grid-1x2 me-2"></i>Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-4 py-3" id="edit-profile-tab" data-bs-toggle="tab" data-bs-target="#edit-profile" type="button" role="tab">
                                <i class="bi bi-pencil-square me-2"></i>Edit Profile
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-4 py-3" id="change-password-tab" data-bs-toggle="tab" data-bs-target="#change-password" type="button" role="tab">
                                <i class="bi bi-shield-lock me-2"></i>Change Password
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                            <h5 class="card-title mt-2" style="margin-bottom: 40px !important;">Profile Overview</h5>

                            @php
                                $fields = [
                                    'Full Name' => $user->name,
                                    'Email' => $user->email,
                                    'Phone' => $user->phone ?: 'Not specified',
                                    'Bank Account Number' => $user->bank_account_number ?: 'Not specified',
                                    'Address' => $user->address ?: 'Not specified',
                                    'Notes' => $user->notes ?: 'No notes available',
                                ];
                            @endphp

                            @foreach($fields as $label => $value)
                                <div class="row mb-4 align-items-center">
                                    <div class="col-md-4 col-lg-3">
                                        <div class="text-muted fw-semibold text-primary">{{ $label }}</div>
                                    </div>
                                    <div class="col-md-8 col-lg-9">
                                        <div class="text-dark fw-medium border-bottom pb-2">{{ $value }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>


                        <!-- Edit Profile Tab -->
                        <div class="tab-pane fade" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
                            <h5 class="card-title mt-3" style="margin-bottom: 40px !important;">Edit Profile Information</h5>

                            <form id="updateProfileForm" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                {{-- <div class="mb-4 text-center">
                                    @if($user->profile_image)
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" id="profile-image-preview" alt="Profile Image" class="avatar-md rounded-circle border shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div id="profile-image-preview" class="avatar-md rounded-circle bg-primary bg-opacity-10 mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                                            <span class="text-primary fw-bold fs-2">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="position-relative d-inline-block">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="changePhotoBtn">
                                            <i class="bi bi-camera me-1"></i> Change Photo
                                        </button>
                                        <input type="file" id="profile_image" name="profile_image" class="position-absolute" style="top: 0; left: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
                                    </div>
                                </div> --}}

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                                        <div class="input-group mb-0">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-person"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" id="name" name="name"
                                                placeholder="Enter full name" value="{{ $user->name }}" required>
                                        </div>
                                        <div class="invalid-feedback" id="name-error"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                                        <div class="input-group mb-0">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control border-start-0" id="email" name="email"
                                                placeholder="Enter email" value="{{ $user->email }}" required>
                                        </div>
                                        <div class="invalid-feedback" id="email-error"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label fw-medium">Phone Number</label>
                                        <div class="input-group mb-0">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-telephone"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" id="phone" name="phone"
                                                placeholder="Enter phone number" value="{{ $user->phone }}">
                                        </div>
                                        <div class="invalid-feedback" id="phone-error"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="bank_account_number" class="form-label fw-medium">Bank Account Number</label>
                                        <div class="input-group mb-0">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-bank"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" id="bank_account_number" name="bank_account_number"
                                                placeholder="Enter bank account number" value="{{ $user->bank_account_number }}">
                                        </div>
                                        <div class="invalid-feedback" id="bank_account_number-error"></div>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="address" class="form-label fw-medium">Address</label>
                                        <div class="input-group mb-0">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-geo-alt"></i>
                                            </span>
                                            <input type="text" class="form-control border-start-0" id="address" name="address"
                                                placeholder="Enter address" value="{{ $user->address }}">
                                        </div>
                                        <div class="invalid-feedback" id="address-error"></div>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="notes" class="form-label fw-medium">Notes</label>
                                        <div class="input-group mb-0">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-sticky"></i>
                                            </span>
                                            <textarea class="form-control border-start-0" id="notes" name="notes" rows="3"
                                                placeholder="Enter notes">{{ $user->notes }}</textarea>
                                        </div>
                                        <div class="invalid-feedback" id="notes-error"></div>
                                    </div>

                                    <div class="col-12 text-end mt-4">
                                        <button type="submit" class="btn btn-primary px-4" id="updateProfileBtn">
                                            <i class="bi bi-check-circle me-2"></i>Save Changes
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                            <h5 class="card-title mb-3" style="margin-bottom: 40px !important;">Change Password</h5>

                            <form id="changePasswordForm" action="{{ route('admin.profile.password') }}" method="POST" class="row g-3">
                                @csrf

                                <div class="col-md-12">
                                    <label for="current_password" class="form-label fw-medium">Current Password <span class="text-danger">*</span></label>
                                    <div class="input-group mb-0">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                        <input type="password" class="form-control border-start-0" id="current_password" name="current_password"
                                            placeholder="Enter current password" required>
                                        <button class="btn btn-light border toggle-password" type="button" data-target="#current_password">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="current_password-error"></div>
                                </div>

                                <div class="col-md-12">
                                    <label for="new_password" class="form-label fw-medium">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group mb-0">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" class="form-control border-start-0" id="new_password" name="new_password"
                                            placeholder="Enter new password" required>
                                        <button class="btn btn-light border toggle-password" type="button" data-target="#new_password">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="new_password-error"></div>
                                    <div class="form-text">Password must be at least 8 characters long</div>
                                </div>

                                <div class="col-md-12">
                                    <label for="new_password_confirmation" class="form-label fw-medium">Confirm New Password <span class="text-danger">*</span></label>
                                    <div class="input-group mb-0">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" class="form-control border-start-0" id="new_password_confirmation" name="new_password_confirmation"
                                            placeholder="Confirm new password" required>
                                        <button class="btn btn-light border toggle-password" type="button" data-target="#new_password_confirmation">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="new_password_confirmation-error"></div>
                                </div>

                                <div class="col-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4" id="changePasswordBtn">
                                        <i class="bi bi-shield-check me-2"></i>Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endpermission
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle password visibility
        $('.toggle-password').on('click', function() {
            const targetId = $(this).data('target');
            const input = $(targetId);
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            }
        });

        // Preview profile image before upload
        $('#profile_image').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if ($('#profile-image-preview').is('div')) {
                        $('#profile-image-preview').replaceWith('<img src="' + e.target.result + '" id="profile-image-preview" alt="Profile Image" class="avatar-md rounded-circle border shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover;">');
                    } else {
                        $('#profile-image-preview').attr('src', e.target.result);
                    }
                }
                reader.readAsDataURL(file);
            }
        });

        // Handle update profile form submission via AJAX
        $('#updateProfileForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#updateProfileBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').attr('disabled', true);
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
                    $('#updateProfileBtn').html('<i class="bi bi-check-circle me-2"></i>Save Changes').attr('disabled', false);

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
                    $('#updateProfileBtn').html('<i class="bi bi-check-circle me-2"></i>Save Changes').attr('disabled', false);
                }
            });
        });

        // Handle change password form submission via AJAX
        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                beforeSend: function() {
                    $('#changePasswordBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...').attr('disabled', true);
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
                            $('#changePasswordForm')[0].reset();
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
                    $('#changePasswordBtn').html('<i class="bi bi-shield-check me-2"></i>Update Password').attr('disabled', false);

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
                    $('#changePasswordBtn').html('<i class="bi bi-shield-check me-2"></i>Update Password').attr('disabled', false);
                }
            });
        });

        // Activate tab based on URL hash
        let activeTab = window.location.hash.replace('#', '');
        if (activeTab) {
            $('#profileTabs button[data-bs-target="#' + activeTab + '"]').tab('show');
        }

        // Update URL hash when tab changes
        $('#profileTabs button').on('click', function() {
            let id = $(this).data('bs-target').replace('#', '');
            window.location.hash = id;
        });
    });
</script>
@endpush
