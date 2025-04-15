@extends('admin.layout.master')
@section('title', 'Root Sounds | Add New Product')
@section('description', 'Add New Product')
@section('content')

<div class="container-fluid mt-4">
    <link rel="stylesheet" href="{{ asset('assets/css/products.css') }}">
    <div class="row">
        <div class="col-12">
            <div class="card p-4 shadow">
                <h3 class="mb-4 text-center product-title" style="font-size: 2.4rem; color: #4154f1;"><i class="bi bi-plus-circle"></i> Add New Product</h3>

                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="addProductForm">
                    @csrf
                    <div class="row g-4">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Basic Information -->
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header custom-card-header">
                                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="detail_name" class="form-label">Detail Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('detail_name') is-invalid @enderror" id="detail_name" name="detail_name" value="{{ old('detail_name') }}" required>
                                        @error('detail_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="index_no" class="form-label">Display Order <i class="bi bi-info-circle-fill text-muted" data-bs-toggle="tooltip" title="Lower numbers appear first on the homepage. Products with the same number will be ordered by creation date."></i></label>
                                        <input type="number" class="form-control @error('index_no') is-invalid @enderror" id="index_no" name="index_no" value="{{ old('index_no', 1) }}" min="1">
                                        <small class="text-muted">Sets the position of this product on the homepage</small>
                                        @error('index_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}">
                                        <small class="text-muted">Leave empty to auto-generate from name</small>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Discount Section -->
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header custom-card-header">
                                    <h5 class="mb-0"><i class="bi bi-tag-fill me-2"></i>Discount</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="discount" class="form-label">Discounted Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" class="form-control @error('discount') is-invalid @enderror"
                                                id="discount" name="discount">
                                            @error('discount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="discountToggle"
                                            name="discountToggle" value="1" {{ old('discountToggle') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="discountToggle">Enable Discounted Amount</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Product Status -->
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header custom-card-header">
                                    <h5 class="mb-0"><i class="bi bi-toggle-on me-2"></i>Product Status</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ old('status') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status">Active</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="featured" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="featured">Featured</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="isFree" name="is_free" value="1" {{ old('is_free') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="isFree">Is Free</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Product Image -->
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header custom-card-header">
                                    <h5 class="mb-0"><i class="bi bi-image me-2"></i>Product Image</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Upload Image <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mt-3 text-center d-none" id="imagePreviewContainer">
                                        <div class="position-relative d-inline-block">
                                            <img id="imagePreview" src="#" alt="Image Preview" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                                            <button id="imageRemove" class="btn btn-sm btn-danger position-absolute" style="top: 5px; right: 5px; z-index: 10;"><i class="bi bi-x-circle"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Half Width Short Description -->
                            <div class="col-6">
                                <div class="card mb-4 border-0 shadow-sm">
                                    <div class="card-header custom-card-header">
                                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Short Description</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="5">{{ old('short_description') }}</textarea>
                                            @error('short_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Half Width Detail Description -->
                            <div class="col-6">
                                <div class="card mb-4 border-0 shadow-sm">
                                    <div class="card-header custom-card-header">
                                        <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Detail Description</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <textarea name="detail_description" class="form-control @error('detail_description') is-invalid @enderror" rows="5">{{ old('detail_description') }}</textarea>
                                            @error('detail_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Full Width Long Description -->
                        <div class="col-12">
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header custom-card-header">
                                    <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>About Product</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <textarea id="my-editor" name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audio Files Section -->
                    <div class="col-12">
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header custom-card-header">
                                <h5 class="mb-0"><i class="bi bi-music-note-beamed me-2"></i>Audio Files</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <p class="text-muted mb-3">Add multiple audio files with their names</p>

                                    <div id="audio-files-container">
                                        <!-- Audio files will be displayed here -->
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" id="add-audio-file" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Add Audio File
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Zip Files Section -->
                    <div class="col-12">
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header custom-card-header">
                                <h5 class="mb-0"><i class="bi bi-music-note-beamed me-2"></i>Download Zip Files</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <p class="text-muted mb-3">Add Zip Files files with their names</p>

                                    <div id="zip-files-container">
                                        <!-- Audio files will be displayed here -->
                                    </div>

                                    <div class="mt-3">
                                        <button type="button" id="add-zip-file" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Add Zip File
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Data -->
                    <div class="col-12">
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header custom-card-header">
                                <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Meta Data</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="5">{{ old('meta_description') }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords (separated by commas)</label>
                                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
                                    @error('meta_keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">e.g. "key1, key2"</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary action-btn">
                                <i class="bi bi-save"></i> Save Product
                                <span class="spinner-border spinner-border-sm d-none spinner" role="status"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {

        /************** Image preview *************/
        $('#image').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#imagePreviewContainer').removeClass('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').removeAttr('src');
                $('#imagePreviewContainer').addClass('d-none');
            }
        });

        $('#imageRemove').click(function(e) {
            e.preventDefault();
            $('#image').val('');
            $('#imagePreview').removeAttr('src');
            $('#imagePreviewContainer').addClass('d-none');
        });

        /************** Audio Files Handling *************/
        // Add new audio file input
        $('#add-audio-file').on('click', function() {
            const index = $('.audio-file-group').length;
            const audioFileHtml = `
                <div class="audio-file-group bg-light p-3 rounded mb-3 position-relative" data-index="${index}">
                    <button type="button" class="btn btn-sm btn-danger remove-audio-file position-absolute" style="top: 10px; right: 10px;">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <div class="row">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Audio Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="audio_names[]" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Full Audio Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="full_names[]" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Author Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="subtitle[]" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Order No <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="order_no[]" value="0" min="0" required>
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Audio File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control audio-file-input" name="audio_files[]" accept="audio/*" required>
                            </div>
                        </div>
                    </div>
                    <div class="audio-preview d-none mt-2">
                        <div class="d-flex align-items-center bg-white p-2 rounded">
                            <i class="bi bi-file-earmark-music text-primary me-2" style="font-size: 1.5rem;"></i>
                            <div class="flex-grow-1">
                                <p class="audio-file-name mb-0 fw-bold"></p>
                            </div>
                            <audio controls class="audio-player" style="max-width: 250px;"></audio>
                        </div>
                    </div>
                </div>
            `;

            $('#audio-files-container').append(audioFileHtml);
        });

        // Remove audio file
        $(document).on('click', '.remove-audio-file', function() {
            $(this).closest('.audio-file-group').fadeOut(300, function() {
                $(this).remove();
            });
        });

        // Preview audio file
        $(document).on('change', '.audio-file-input', function() {
            const file = this.files[0];
            const parent = $(this).closest('.audio-file-group');
            const previewDiv = parent.find('.audio-preview');
            const audioPlayer = parent.find('.audio-player');
            const fileNameDisplay = parent.find('.audio-file-name');

            if (file) {
                const fileName = file.name;
                const fileURL = URL.createObjectURL(file);

                fileNameDisplay.text(fileName);
                audioPlayer.attr('src', fileURL);
                previewDiv.removeClass('d-none');
            } else {
                previewDiv.addClass('d-none');
                audioPlayer.removeAttr('src');
            }
        });


        /************** Zip Files Handling *************/
        // Add new zip file input
        $('#add-zip-file').on('click', function() {
            const index = $('.zip-file-group').length;
            const zipFileHtml = `
                <div class="zip-file-group bg-light p-3 rounded mb-3 position-relative" data-index="${index}">
                    <button type="button" class="btn btn-sm btn-danger remove-zip-file position-absolute" style="top: 10px; right: 10px;">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <div class="row">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Zip Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="zip_names[]" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Zip File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control zip-file-input" name="zip_files[]" accept="zip/*" required>
                            </div>
                        </div>
                    </div>
                    <div class="zip-preview d-none mt-2">
                        <div class="d-flex align-items-center bg-white p-2 rounded">
                            <i class="bi bi-file-earmark-zip-fill text-primary me-2" style="font-size: 1.5rem;"></i>
                            <div class="flex-grow-1">
                                <p class="zip-file-name mb-0 fw-bold"></p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#zip-files-container').append(zipFileHtml);
        });

        // Remove zip file
        $(document).on('click', '.remove-zip-file', function() {
            $(this).closest('.zip-file-group').fadeOut(300, function() {
                $(this).remove();
            });
        });

        // Preview zip file
        $(document).on('change', '.zip-file-input', function() {
            const file = this.files[0];
            const parent = $(this).closest('.zip-file-group');
            const previewDiv = parent.find('.zip-preview');
            const zipPlayer = parent.find('.zip-player');
            const fileNameDisplay = parent.find('.zip-file-name');

            if (file) {
                const fileName = file.name;
                const fileURL = URL.createObjectURL(file);

                fileNameDisplay.text(fileName);
                zipPlayer.attr('src', fileURL);
                previewDiv.removeClass('d-none');
            } else {
                previewDiv.addClass('d-none');
                zipPlayer.removeAttr('src');
            }
        });
    });
</script>
@endpush

@endsection
