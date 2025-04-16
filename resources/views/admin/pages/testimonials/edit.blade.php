@extends('admin.layout.master')
@section('title', 'Stock Management | Edit Testimonial')
@section('description', 'Edit Customer Testimonial')
@section('content')

<style>
    .form-label {
        font-weight: 600;
    }

    .btn-primary {
        background-color: #4154f1;
        border-color: #4154f1;
    }

    .btn-primary:hover {
        background-color: #a95412;
        color: white;
        border-color: #a95412;
    }

    .form-check-input:checked {
        background-color: #4154f1;
        border-color: #4154f1;
    }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        font-size: 1.5em;
        justify-content: flex-end;
        align-items: center;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        color: #ddd;
        cursor: pointer;
        padding: 0 0.1em;
        transition: color 0.2s;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #ffc107;
    }

    .card-title {
        color: #4154f1;
    }

    .current-image-container {
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-align: center;
    }

    .current-image {
        max-width: 150px;
        border-radius: 50%;
    }
</style>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0"><i class="bi bi-chat-quote"></i> Edit Testimonial</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reviewer_name" class="form-label">Reviewer Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="reviewer_name" name="reviewer_name" value="{{ old('reviewer_name', $testimonial->reviewer_name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reviewer_role" class="form-label">Reviewer Role <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="reviewer_role" name="reviewer_role" value="{{ old('reviewer_role', $testimonial->reviewer_role) }}" required>
                                    <small class="text-muted">e.g., Film Composer, Music Producer, etc.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rating <span class="text-danger">*</span></label>
                            <div class="star-rating">
                                <input type="radio" id="star5" name="stars" value="5" {{ old('stars', $testimonial->stars) == 5 ? 'checked' : '' }} />
                                <label for="star5" title="5 stars"><i class="bi bi-star-fill"></i></label>

                                <input type="radio" id="star4" name="stars" value="4" {{ old('stars', $testimonial->stars) == 4 ? 'checked' : '' }} />
                                <label for="star4" title="4 stars"><i class="bi bi-star-fill"></i></label>

                                <input type="radio" id="star3" name="stars" value="3" {{ old('stars', $testimonial->stars) == 3 ? 'checked' : '' }} />
                                <label for="star3" title="3 stars"><i class="bi bi-star-fill"></i></label>

                                <input type="radio" id="star2" name="stars" value="2" {{ old('stars', $testimonial->stars) == 2 ? 'checked' : '' }} />
                                <label for="star2" title="2 stars"><i class="bi bi-star-fill"></i></label>

                                <input type="radio" id="star1" name="stars" value="1" {{ old('stars', $testimonial->stars) == 1 ? 'checked' : '' }} />
                                <label for="star1" title="1 star"><i class="bi bi-star-fill"></i></label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="review_text" class="form-label">Review Text <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="review_text" name="review_text" rows="5" required>{{ old('review_text', $testimonial->review_text) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Testimonial
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
        // Show validation errors with SweetAlert2
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach',
                confirmButtonColor: '#4154f1'
            });
        @endif

        // Show success message with SweetAlert2
        // @if(session('success'))
        //     Swal.fire({
        //         icon: 'success',
        //         title: 'Success',
        //         text: "{{ session('success') }}",
        //         timer: 2000,
        //         showConfirmButton: false,
        //     });
        // @endif
    });
</script>
@endpush
