@php
    use App\Models\Orders;
    use App\Models\Product;
    use App\Models\Seller;
@endphp
@extends('admin.layout.master')
@section('title', 'Root Sounds | Admin Dashboard')
@section('description', 'Root Sounds Admin Dashboard')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div>
                                <h2 class="fs-3 fw-bold mb-0">Dashboard</h2>
                                <p class="text-muted mb-0">Welcome to Root Studio Admin Panel</p>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-light text-dark p-2">
                                    <i class="bi bi-calendar3 me-1"></i> {{ date('F d, Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Products Stats -->
                            <div class="col-lg-4 col-md-6" style="width: 290px;">
                                <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 dashboard-stats-box overflow-hidden">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon me-3 d-flex align-items-center justify-content-center rounded-circle"
                                                     style="background-color: rgba(195, 96, 20, 0.1); width: 60px; height: 60px;">
                                                    <i class="bi bi-box" style="color: #4154f1; font-size: 1.8rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-muted fs-6 mb-1">Total Products</h6>
                                                    <h3 class="fw-bold mb-0">{{ Product::all()->count() }}</h3>
                                                </div>
                                            </div>
                                            <div class="progress mt-3" style="height: 4px;">
                                                <div class="progress-bar" role="progressbar" style="width: 100%; background-color: #4154f1;"
                                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Free Products -->
                             <div class="col-lg-4 col-md-6" style="width: 290px;">
                                <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 dashboard-stats-box overflow-hidden">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon me-3 d-flex align-items-center justify-content-center rounded-circle"
                                                     style="background-color: rgba(195, 96, 20, 0.1); width: 60px; height: 60px;">
                                                     <i class="bi bi-gift" style="color: #4154f1; font-size: 1.8rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-muted fs-6 mb-1">Free Products</h6>
                                                    <h3 class="fw-bold mb-0">{{ Product::where('is_free', 1)->count() }}</h3>
                                                </div>
                                            </div>
                                            <div class="progress mt-3" style="height: 4px;">
                                                <div class="progress-bar" role="progressbar" style="width: 100%; background-color: #4154f1;"
                                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Total Orders -->
                            <div class="col-lg-4 col-md-6" style="width: 290px;">
                                <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 dashboard-stats-box overflow-hidden">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center">
                                                <div class="stats-icon me-3 d-flex align-items-center justify-content-center rounded-circle"
                                                     style="background-color: rgba(195, 96, 20, 0.1); width: 60px; height: 60px;">
                                                     <i class="bi bi-box-seam" style="color: #4154f1; font-size: 1.8rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-muted fs-6 mb-1">Total Orders</h6>
                                                    <h3 class="fw-bold mb-0">{{ Orders::where('status', 'completed')->count() }}</h3>
                                                </div>
                                            </div>
                                            <div class="progress mt-3" style="height: 4px;">
                                                <div class="progress-bar" role="progressbar" style="width: 100%; background-color: #4154f1;"
                                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>


                        </div>

                        <!-- Recent Activity Section -->
                        <!-- <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-header bg-white py-3 border-0">
                                        <h5 class="mb-0 fw-bold">Root Sounds Overview</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="alert alert-light border-start border-5"
                                             style="border-left-color: #4154f1 !important;">
                                            <div class="d-flex">
                                                <div class="me-3">
                                                    <i class="bi bi-info-circle-fill" style="color: #4154f1; font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h5 class="alert-heading mb-1">Welcome to Root Sounds Admin</h5>
                                                    <p class="mb-0">This dashboard gives you an overview of your sample library collection and system statistics. Use the sidebar to navigate to different sections.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {{-- <script>
        $(document).ready(function() {
            $(".admin_dashboardli").addClass("active");
        });
    </script> --}}
@endpush
