@extends('admin.layout.master')
@section('title', 'Stock Management | Admin | Settings')
@section('description', 'Admin Settings')
@section('content')
    <div class="container-fluid mt-4">
        <link rel="stylesheet" href="{{ asset('assets/css/products.css') }}">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 p-4 shadow">
                    <h3 class="mb-4 text-center" style="font-size: 2.4rem; color: #4154f1;">
                        <i class="bi bi-gear-fill"></i> Site Settings
                    </h3>
                    <div class="row mt-4">
                        <form id="updateSettings" action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <div class="col-md-12">
                                <div class="card mb-4 p-4 border-0 shadow-sm">
                                    <div class="card-header custom-card-header">
                                        <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>General Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="{{ $settings->name ?? '' }}">
                                                <span class="invalid-feedback" id="name-error"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="login_email" class="form-label">Login Email</label>
                                                <input type="email" class="form-control" id="login_email" name="login_email"
                                                    value="{{ $settings->login_email ?? '' }}">
                                                <span class="invalid-feedback" id="login_email-error"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="facebook" class="form-label">Facebook</label>
                                                <input type="text" class="form-control" id="facebook" name="facebook"
                                                    value="{{ $settings->facebook ?? '' }}">
                                                <span class="invalid-feedback" id="facebook-error"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="instagram" class="form-label">Twitter</label>
                                                <input type="text" class="form-control" id="instagram" name="instagram"
                                                    value="{{ $settings->instagram ?? '' }}">
                                                <span class="invalid-feedback" id="instagram-error"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="linkedin" class="form-label">Linkedin</label>
                                                <input type="text" class="form-control" id="linkedin" name="linkedin"
                                                    value="{{ $settings->linkedin ?? '' }}">
                                                <span class="invalid-feedback" id="linkedin-error"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="soundcloud" class="form-label">Soundcloud</label>
                                                <input type="text" class="form-control" id="soundcloud" name="soundcloud"
                                                    value="{{ $settings->soundcloud ?? '' }}">
                                                <span class="invalid-feedback" id="soundcloud-error"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="youtube" class="form-label">Youtube</label>
                                                <input type="text" class="form-control" id="youtube" name="youtube"
                                                    value="{{ $settings->youtube ?? '' }}">
                                                <span class="invalid-feedback" id="youtube-error"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="info_phone" class="form-label">Info Phone</label>
                                                <input type="text" class="form-control" id="info_phone" name="info_phone"
                                                    value="{{ $settings->info_phone ?? '' }}">
                                                <span class="invalid-feedback" id="info_phone-error"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="info_email" class="form-label">Info Email</label>
                                                <input type="email" class="form-control" id="info_email" name="info_email"
                                                    value="{{ $settings->info_email ?? '' }}">
                                                <span class="invalid-feedback" id="info_email-error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4 p-4 border-0 shadow-sm">
                                    <div class="card-header custom-card-header">
                                        <h5 class="mb-0"><i class="bi bi-credit-card-fill me-2"></i>Payment Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="paypal_client_id" class="form-label">Paypal Client ID</label>
                                                <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id"
                                                    value="{{ $settings->paypal_client_id ?? '' }}">
                                                <span class="invalid-feedback" id="paypal_client_id-error"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="paypal_secret_key" class="form-label">Paypal Secret Key</label>
                                                <input type="text" class="form-control" id="paypal_secret_key" name="paypal_secret_key"
                                                    value="{{ $settings->paypal_secret_key ?? '' }}">
                                                <span class="invalid-feedback" id="paypal_secret_key-error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 act_button">
                                        <i class="bi bi-save me-2"></i>Save Settings
                                        <span class="spinner-border spinner-border-sm d-none spinner" role="status"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-4 p-4 shadow">
                    <h3 class="mb-4 text-center" style="font-size: 2.4rem; color: #4154f1;">
                        <i class="bi bi-lock-fill"></i> Change Password
                    </h3>
                    <div class="row mt-4">
                        <form id="changePasswordForm" action="{{ route('admin.settings.password') }}" method="POST">
                            @csrf
                            <div class="col-md-12">
                                <div class="card mb-4 p-4 border-0 shadow-sm">
                                    <div class="card-header custom-card-header">
                                        <h5 class="mb-0"><i class="bi bi-shield-lock-fill me-2"></i>Update Password</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="old_password" class="form-label">Current Password</label>
                                                <input type="password" class="form-control" id="old_password" name="old_password">
                                                <span class="invalid-feedback" id="old_password-error"></span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="new_password" class="form-label">New Password</label>
                                                <input type="password" class="form-control" id="new_password" name="new_password">
                                                <span class="invalid-feedback" id="new_password-error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 act_button2">
                                        <i class="bi bi-key me-2"></i>Change Password
                                        <span class="spinner-border spinner-border-sm d-none spinner2" role="status"></span>
                                    </button>
                                </div>
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
            $(".admin-settings").addClass("active");

            $("#updateSettings").submit(function(e) {
                e.preventDefault();

                $('.invalid-feedback').hide();
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    beforeSend: function(){
                        $('.act_button').addClass('disabled');
                        $('.spinner').removeClass('d-none');
                    },
                    success: function(response) {
                        $('.act_button').removeClass('disabled');
                        $('.spinner').addClass('d-none');
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonColor: '#4154f1',
                            });
                        }
                    },
                    error: function(xhr) {
                        $('.act_button').removeClass('disabled');
                        $('.spinner').addClass('d-none');
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]).show();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred. Please try again.',
                                showConfirmButton: true,
                                confirmButtonColor: '#4154f1',
                            });
                        }
                    }
                });
            });

            // Password change form submit
            $("#changePasswordForm").submit(function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.invalid-feedback').hide();
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    beforeSend: function(){
                        $('.act_button2').addClass('disabled');
                        $('.spinner2').removeClass('d-none');
                    },
                    success: function(response) {
                        $('.act_button2').removeClass('disabled');
                        $('.spinner2').addClass('d-none');
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            });

                            $('#old_password').val('');
                            $('#new_password').val('');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonColor: '#4154f1',
                            });
                        }
                    },
                    error: function(xhr) {
                        $('.act_button2').removeClass('disabled');
                        $('.spinner2').addClass('d-none');
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]).show();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred. Please try again.',
                                showConfirmButton: true,
                                confirmButtonColor: '#4154f1',
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
