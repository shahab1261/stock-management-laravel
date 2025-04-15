@extends('admin.layout.master')

@section('title', 'License Agreement - Root Sounds')
@section('description', 'Edit license agreement page')

@section('content')

<div class="container-fluid mt-4">
    <link rel="stylesheet" href="{{ asset('assets/css/products.css') }}">
    <div class="card p-4 shadow">
        <h3 class="mb-4 text-center terms-title" style="font-size: 2.4rem; color: #4154f1;"><i class="bi bi-file-earmark-text"></i> License Agreement</h3>

        <div class="row">
            <div class="col-12">
                <form id="licenseForm" action="{{ route('admin.license.update') }}" method="POST">
                    @csrf
                    <input type="hidden" id="license_id" name="license_id">

                    <!-- License Agreement Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header custom-card-header">
                            <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>License Agreement</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="license" class="form-label">License Agreement</label>
                                <textarea id="my-editor" name="content" class="form-control @error('license') is-invalid @enderror" cols="30" rows="10">{{ old('license', $license->content) }}</textarea>
                                @error('license')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SEO Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header custom-card-header">
                            <h5 class="mb-0"><i class="bi bi-search me-2"></i>SEO</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" id="meta_title" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $license->meta_title) }}">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea id="meta_description" name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="5">{{ old('meta_description', $license->meta_description) }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords (separated by commas)</label>
                                <input type="text" id="meta_keywords" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror" value="{{ old('meta_keywords', $license->meta_keywords) }}">
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary action-btn">
                            <i class="bi bi-save"></i> Update
                            <span class="spinner-border spinner-border-sm d-none spinner" role="status"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
