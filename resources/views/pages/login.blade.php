@extends('layouts.app')
@section('title', 'Admin Login | Stock Management')
@section('description', 'Stock Management Admin Login')
@section('content')
    @php
        $settings = App\Models\Management\Settings::first();
    @endphp
    <style>
        #login-btn:hover {
            background-color: #5868f4 !important;
        }
    </style>
    <div class="login-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="login-form-wrapper card shadow-lg rounded-4 border-0 p-4 p-md-5" data-aos="fade-up">
                        <!-- Logo -->
                        <div class="text-center mb-2">
                            <img src="{{ asset($settings->logo_path) }}" alt="Stock Management Logo" class="img-fluid"
                                style="max-height: 80px;">
                            <h2 class="mt-3 fw-bold">Admin Login</h2>
                            <p class="text-muted">Enter your credentials to access the dashboard</p>
                        </div>

                        <!-- Login Form -->
                        <form id="login-form" class="login-form" method="POST" action="{{ route('admin.login.post') }}">
                            @csrf

                            <!-- Error Messages -->
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Email Input -->
                            <div class="form-group mb-3">
                                <label for="email" class="form-label fw-medium">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" name="email" id="email"
                                        class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                        placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div class="form-group mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="password" class="form-label fw-medium">Password</label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="password" id="password"
                                        class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                                        placeholder="Enter your password" required>
                                    <button type="button" class="input-group-text bg-light border-start-0 password-toggle">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-medium text-white" id = "login-btn"
                                    style="background-color: #4154f1; border-color: #4154f1">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Log In
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 pt-3 border-top text-center">
                            <p class="mb-0 text-muted small"> {{ date('Y') }} {{ $settings->company_name }}. All Rights Reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('scripts')
    <script>
        window.onload = function() {
            var toggleButton = document.querySelector('.password-toggle');
            var passwordInput = document.getElementById('password');

            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    var icon = this.querySelector('i');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }
                });
            }

            var loginForm = document.getElementById('login-form');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    var loginBtn = document.getElementById('login-btn');
                    loginBtn.disabled = true;
                    loginBtn.classList.add('spinner');
                    loginBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...`;

                    let email = document.getElementById('email').value;
                    let password = document.getElementById('password').value;
                    let remember = document.getElementById('remember').checked ? 1 : 0;

                    fetch('{{ route('admin.login.post') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            email: email,
                            password: password,
                            remember: remember,
                            _token: '{{ csrf_token() }}'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        loginBtn.disabled = false;
                        loginBtn.classList.remove('spinner');
                        loginBtn.innerHTML = 'Log In';
                        if (data.success) {
                            window.location.href = '{{ route('admin.dashboard') }}';
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4154f1'
                            });
                        }
                    })
                    .catch(error => {
                        loginBtn.disabled = false;
                        loginBtn.classList.remove('spinner');
                        loginBtn.innerHTML = 'Log In';
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4154f1'
                       });
                    });
                });
            }
        };
    </script>
@endsection
@endsection
