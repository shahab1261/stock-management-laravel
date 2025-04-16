@extends('admin.layout.master')
@section('title', 'Stock Management | Free Product Requests')
@section('description', 'Free Product Requests')
@section('content')

<style>
    .request-title {
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
    #requestsTable {
        border-collapse: separate;
        border-spacing: 0;
    }

    #requestsTable thead th {
        font-weight: 600;
        padding: 12px 15px;
    }

    #requestsTable tbody td {
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
    .request-modal {
        border-radius: 10px;
        overflow: hidden;
    }

    .request-modal .modal-body {
        padding: 20px;
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

    /* Responsive adjustments */
    @media (max-width: 767px) {
        .request-title {
            font-size: 2rem;
        }

        .request-modal .modal-body {
            padding: 15px;
        }

        #requestsTable tbody td {
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
        <h3 class="mb-3 text-center request-title"><i class="bi bi-gift"></i> Free Product Requests</h3>

        <div class="table-responsive">
            <table id="requestsTable" class="table table-striped table-hover w-100">
                <thead class="custom-table-header">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Product</th>
                        <th class="text-center">Newsletter</th>
                        <th class="text-center">Country</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr class="{{ $request->processed ? 'table-success' : '' }}">
                            <td class="text-center">{{ $request->id }}</td>
                            <td class="text-center">{{ $request->created_at->format('Y-m-d') }}</td>
                            <td>
                                <strong>{{ $request->name }}</strong><br>
                                <small class="text-muted">{{ $request->email }}</small>
                            </td>
                            <td class="text-center">
                                <strong>{{ $request->product->name }}</strong>
                            </td>
                            <td>
                                @if($request->subscribe_newsletter)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Subscribed</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Not Subscribed</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $request->country }}
                            </td>
                            <td class="text-center">
                                @if ($request->processed)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Processed</span>
                                @else
                                    <span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary action-btn approve-request"
                                        data-request-id="{{ $request->id }}"
                                        {{ $request->processed ? 'disabled' : '' }}>
                                    <i class="bi bi-envelope"></i> Email Download Link
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
        $('#requestsTable').DataTable({
            responsive: false,
            scrollX: true,
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: '<"dt-controls-row"lf>rtip',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search requests..."
            },
            order: [[6, 'asc'], [1, 'desc']]
        });

        $('.dataTables_filter input').addClass('form-control form-control-sm ms-2');
        $('.dataTables_length select').addClass('form-control form-control-sm mx-2');

        $(document).on('click', '.approve-request', function() {
            const button = $(this);
            const requestId = button.data('request-id');

            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

            $.ajax({
                url: `/admin/free-product-requests/${requestId}/approve`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: true,
                            confirmButtonColor: '#4154f1',
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4154f1',
                        });
                        button.prop('disabled', false).html('<i class="bi bi-envelope"></i> Email Download Link');
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: 'An error occurred while processing the request.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1',
                    });
                    button.prop('disabled', false).html('<i class="bi bi-envelope"></i> Email Download Link');
                }
            });
        });
    });
</script>
@endpush
