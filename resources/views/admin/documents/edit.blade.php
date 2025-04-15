@extends('admin.layout.master')
@section('title', 'Root Sounds | Edit Document')
@section('description', 'Edit Document')
@section('content')

<style>
    .document-title {
        font-size: 2.6rem;
        color: #4154f1;
    }

    .form-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form-label {
        font-weight: 600;
        color: #333;
    }

    .form-control:focus {
        border-color: #4154f1;
        box-shadow: 0 0 0 0.2rem rgba(195, 96, 20, 0.25);
    }

    .custom-file-input {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .custom-file-input input[type="file"] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .custom-file-label {
        display: block;
        padding: 0.375rem 0.75rem;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        cursor: pointer;
    }

    .custom-file-label:hover {
        background-color: #e9ecef;
    }

    .btn-submit {
        background-color: #4154f1;
        border-color: #4154f1;
        color: white;
        padding: 0.5rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background-color: #a95412;
        border-color: #a95412;
        transform: translateY(-2px);
    }

    .btn-cancel {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
        padding: 0.5rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background-color: #5a6268;
        border-color: #545b62;
        transform: translateY(-2px);
    }

    .current-file {
        background-color: #f8f9fa;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        margin-top: 0.5rem;
    }

    .current-file a {
        color: #4154f1;
        text-decoration: none;
    }

    .current-file a:hover {
        text-decoration: underline;
    }

    @media (max-width: 767px) {
        .document-title {
            font-size: 2rem;
        }
    }
</style>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card form-card">
                <div class="card-body p-4">
                    <h3 class="mb-4 text-center document-title">
                        <i class="bi bi-file-earmark-text"></i> Edit Document
                    </h3>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.documents.update', $document) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="form-label">Document Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $document->name) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="file" class="form-label">Document File</label>
                            <div class="current-file">
                                <i class="bi bi-file-earmark-text"></i> Current file:
                                <a href="{{ Storage::url($document->file_path) }}" target="_blank">
                                    {{ basename($document->file_path) }}
                                </a>
                            </div>
                            <div class="custom-file-input mt-2">
                                <input type="file" class="form-control" id="file" name="file">
                                <label for="file" class="custom-file-label">Choose new file...</label>
                            </div>
                            <small class="text-muted">Supported formats: PDF, DOC, DOCX, XLS, XLSX. Max size: 10MB</small>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.documents.index') }}" class="btn btn-cancel text-white">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-submit text-white">
                                <i class="bi bi-save"></i> Update Document
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
    // Update file input label with selected filename
    document.getElementById('file').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var label = e.target.nextElementSibling;
        label.textContent = fileName;
    });
</script>
@endpush
