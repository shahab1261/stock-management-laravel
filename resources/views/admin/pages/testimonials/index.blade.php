@extends('admin.layout.master')
@section('title', 'Root Sounds | Testimonials')
@section('description', 'Manage Customer Testimonials')
@section('content')

<style>
    .testimonial-title {
        font-size: 2.4rem;
        color: #4154f1;
    }

    .btn-primary:hover {
        background-color: #a95412;
        color: white;
        border-color: #a95412;
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
    #testimonialsTable {
        border-collapse: separate;
        border-spacing: 0;
    }

    #testimonialsTable thead th {
        font-weight: 600;
        padding: 12px 15px;
    }

    #testimonialsTable tbody td {
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

    /* Enhanced modal styling */
    .testimonial-modal {
        border-radius: 10px;
        overflow: hidden;
    }

    .testimonial-modal .modal-body {
        padding: 20px;
    }

    .testimonial-table th {
        background-color: rgba(195, 96, 20, 0.1);
        width: 30%;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        padding: 0.5rem 0;
    }

    .dt-controls-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    /* Star rating styling */
    .stars-display .bi-star-fill {
        color: #ffc107;
    }

    /* Responsive adjustments */
    @media (max-width: 767px) {
        .testimonial-title {
            font-size: 2rem;
        }

        .testimonial-modal .modal-body {
            padding: 15px;
        }

        #testimonialsTable tbody td {
            padding: 10px;
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
        }
    }
</style>

<div class="container-fluid mt-4">
    <div class="card p-4 shadow">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="testimonial-title"><i class="bi bi-chat-quote"></i> Customer Testimonials</h3>
            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary" style="background-color: #4154f1; border-color: #4154f1;">
                <i class="bi bi-plus-lg"></i> Add New Testimonial
            </a>
        </div>

        <div class="table-responsive">
            <table id="testimonialsTable" class="table table-striped table-hover w-100">
                <thead class="custom-table-header">
                    <tr>
                        <th class="text-center">Id</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Stars</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($testimonials as $testimonial)
                        <tr>
                            <td class="text-center">{{ $testimonial->id }}</td>
                            <td><strong>{{ $testimonial->reviewer_name }}</strong></td>
                            <td>{{ $testimonial->reviewer_role }}</td>
                            <td>
                                <div class="stars-display">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $testimonial->stars)
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="btn btn-warning btn-sm action-btn">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button class="btn btn-danger btn-sm action-btn delete-testimonial"
                                        data-id="{{ $testimonial->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
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
        $('#testimonialsTable').DataTable({
            responsive: true,
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: '<"dt-controls-row"lf>rtip',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search testimonials..."
            },
            order: []
        });

        $('.dataTables_filter input').addClass('form-control form-control-sm ms-2');
        $('.dataTables_length select').addClass('form-control form-control-sm mx-2');

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

        // Show error message with SweetAlert2
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                timer: 2000,
                showConfirmButton: false,
            });
        @endif

        // Confirm delete with SweetAlert2 instead of using the modal
        $('.delete-testimonial').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4154f1',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("admin.testimonials.destroy", "") }}/' + id;
                    form.style.display = 'none';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
