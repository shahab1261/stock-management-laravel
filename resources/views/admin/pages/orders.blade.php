@extends('admin.layout.master')
@section('title', 'Stock Management | Orders')
@section('description', 'Orders')
@section('content')

<style>
    .order-title {
        font-size: 2.6rem;
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
    #ordersTable {
        border-collapse: separate;
        border-spacing: 0;
    }

    #ordersTable thead th {
        font-weight: 600;
        padding: 12px 15px;
    }

    #ordersTable tbody td {
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
    .order-modal {
        border-radius: 10px;
        overflow: hidden;
    }

    .order-modal .modal-body {
        padding: 20px;
    }

    .order-table th {
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

    /* Responsive adjustments */
    @media (max-width: 767px) {
        .order-title {
            font-size: 2rem;
        }

        .order-modal .modal-body {
            padding: 15px;
        }

        #ordersTable tbody td {
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
        <h3 class="mb-3 text-center order-title"><i class="bi bi-cart-check"></i> All Orders</h3>

        <div class="table-responsive">
            <table id="ordersTable" class="table table-striped table-hover w-100">
                <thead class="custom-table-header">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Product</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Last Email Sent</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @if($order->status != 'canceled' && $order->status != 'pending')
                            <tr>
                                <td class="text-center">{{ $order->id }}</td>
                                <td class="text-center">{{ $order->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <strong>{{ $order->buyer_name }}</strong><br>
                                    <small class="text-muted">{{ $order->buyer_email }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('product.show', $order->product->slug) }}" class="text-decoration-none">
                                        <strong class="text-dark">{{ $order->product->name }}</strong>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <strong>${{ number_format($order->price, 2) }}</strong>
                                </td>
                                <td class="text-center">
                                    @if ($order->status === 'completed')
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Completed</span>
                                    @elseif ($order->status === 'pending')
                                        <span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>
                                    @elseif($order->status === 'canceled')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Cancelled</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Failed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($order->email_send_date)
                                        <small>{{ \Carbon\Carbon::parse($order->email_send_date)->format('Y-m-d H:i') }} UTC</small>
                                    @else
                                        <small class="text-muted"></small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary action-btn resend-download"
                                            data-order-id="{{ $order->id }}"
                                            {{ $order->status !== 'completed' ? 'disabled' : '' }}>
                                        <i class="bi bi-envelope"></i> Resend Link
                                    </button>
                                </td>
                            </tr>
                        @endif
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
        $('#ordersTable').DataTable({
            responsive: false,
            scrollX: true,
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            dom: '<"dt-controls-row"lf>rtip',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search orders..."
            },
            order:[]
        });

        $('.dataTables_filter input').addClass('form-control form-control-sm ms-2');
        $('.dataTables_length select').addClass('form-control form-control-sm mx-2');

        $(document).on('click', '.resend-download', function() {
            const button = $(this);
            const orderId = button.data('order-id');

            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');

            $.ajax({
                url: `/admin/orders/${orderId}/resend-download`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload();
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
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.',
                        showConfirmButton: true,
                        confirmButtonColor: '#4154f1'
                    });
                },
                complete: function() {
                    button.prop('disabled', false).html('<i class="bi bi-envelope"></i> Resend Link');
                }
            });
        });
    });
</script>
@endpush
