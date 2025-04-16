@extends('admin.layout.master')
@section('title', 'Stock Management | Documents')
@section('description', 'Manage Documents')
@section('content')

<style>
    .document-title {
        font-size: 2.4rem;
        color: #4154f1;
    }

    /* Custom styles for the table and modal */
    .custom-table-header {
        background-color: #4154f1 !important;
        color: white;
        border: none;
    }

    .custom-modal-header {
        background-color: #4154f1 !important;
        color: white;
        border-bottom: 2px solid #a95412;
    }

    .btn-close {
        filter: brightness(0) invert(1);
    }

    /* Enhanced table styles */
    #documentsTable {
        border-collapse: separate;
        border-spacing: 0;
    }

    #documentsTable thead th {
        font-weight: 600;
        padding: 12px 15px;
    }

    #documentsTable tbody td {
        vertical-align: middle;
        padding: 12px 15px;
    }

    /* Button styling */
    .action-btn {
        margin: 0 2px;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Copy button styling */
    .copy-path {
        background-color: #4154f1;
        border-color: #4154f1;
    }

    .copy-path:hover {
        background-color: #ca681d;
        border-color: #ce681d;
        color: white;
    }

    /* New Document Button */
    .btn-new-document {
        background-color: #4154f1;
        border-color: #4154f1;
        color: white;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }

    .btn-new-document:hover {
        background-color: #a95412;
        border-color: #a95412;
        transform: translateY(-2px);
        color: white;
    }

    /* Enhanced modal styling */
    .document-modal {
        border-radius: 10px;
        overflow: hidden;
    }

    .document-modal .modal-body {
        padding: 20px;
    }

    .document-table th {
        background-color: rgba(195, 96, 20, 0.1);
        width: 30%;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        padding: 0.5rem 0;
    }

    /* Custom DataTable control positioning */
    div.dataTables_wrapper div.dataTables_filter {
        text-align: right;
        margin-top: 10px;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0.5em;
        width: 200px;
    }

    div.dataTables_wrapper div.dataTables_length {
        float: left;
        margin-top: 10px;
    }

    /* File badge styling */
    .file-badge {
        background-color: #6c757d;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Responsive adjustments */
    @media (max-width: 767px) {
        .document-title {
            font-size: 2rem;
        }

        .document-modal .modal-body {
            padding: 15px;
        }

        #documentsTable tbody td {
            padding: 10px;
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            margin-bottom: 5px;
            display: inline-block;
        }
    }

    @media (max-width: 576px) {
        .action-btn {
            display: block;
            width: 100%;
            margin-bottom: 5px;
        }
    }
</style>

<div class="container-fluid mt-4">
    <div class="card p-4 shadow">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="document-title mb-0"><i class="bi bi-file-earmark-text"></i>All Documents</h3>
            <a href="{{ route('admin.documents.create') }}" class="btn btn-new-document">
                <i class="bi bi-plus-lg"></i> New Document
            </a>
        </div>

        <div class="table-responsive">
            <table id="documentsTable" class="table table-striped table-hover w-100">
                <thead class="custom-table-header">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">File</th>
                        <th class="text-center">Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $document)
                        <tr>
                            <td class="text-center">{{ $document->id }}</td>
                            <td class="text-center"><strong>{{ $document->name }}</strong></td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm action-btn copy-path"
                                  data-path="{{ url(Storage::url($document->file_path)) }}"
                                  title="Copy Path to Clipboard">
                                    <i class="bi bi-clipboard"></i> Copy Path
                                </button>
                            </td>
                            <td class="text-center">{{ $document->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-warning btn-sm action-btn">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm action-btn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#documentsTable').DataTable({
            responsive: false,
            scrollX: true,
            paging: true,
            pageLength: 10,
            order: [[0, 'desc']],
            dom: '<"row"<"col-md-6"l><"col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-5"i><"col-sm-7"p>><"row"<"col-sm-12"B>>',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Export to Excel',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: [0, 1, 3]
                    }
                }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search documents...",
                lengthMenu: "_MENU_ documents per page",
                info: "Showing _START_ to _END_ of _TOTAL_ documents",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            }
        });

        // Handle delete confirmation with SweetAlert
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            const form = this;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4154f1',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Copy Path to Clipboard functionality
        $('.copy-path').on('click', function() {
            const path = $(this).data('path');

            // Use modern Clipboard API if available, otherwise fall back to execCommand
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(path)
                    .then(() => {
                        showCopySuccess();
                    })
                    .catch(() => {
                        fallbackCopyToClipboard(path);
                    });
            } else {
                fallbackCopyToClipboard(path);
            }
        });

        // Fallback copy method for browsers that don't support Clipboard API
        function fallbackCopyToClipboard(text) {
            const tempInput = document.createElement('input');
            tempInput.style.position = 'absolute';
            tempInput.style.left = '-1000px';
            tempInput.style.top = '-1000px';
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopySuccess();
                } else {
                    showCopyError();
                }
            } catch (err) {
                showCopyError();
            }

            document.body.removeChild(tempInput);
        }

        // Show success message
        function showCopySuccess() {
            Swal.fire({
                title: 'Copied!',
                text: 'Path has been copied to clipboard',
                icon: 'success',
                toast: false,
                position: 'center',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#4154f1'
            });
        }

        // Show error message
        function showCopyError() {
            Swal.fire({
                title: 'Error',
                text: 'Could not copy to clipboard. Please try again or copy manually.',
                icon: 'error',
                toast: false,
                position: 'center',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#4154f1'
            });
        }
    });
</script>
@endpush
