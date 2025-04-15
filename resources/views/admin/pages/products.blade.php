@extends('admin.layout.master')
@section('title', 'Root Sounds | All Products')
@section('description', 'All Products')
@section('content')

<style>
    .product-title {
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
    #productsTable {
        border-collapse: separate;
        border-spacing: 0;
    }

    #productsTable thead th {
        font-weight: 600;
        padding: 12px 15px;
    }

    #productsTable tbody td {
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
    .product-modal {
        border-radius: 10px;
        overflow: hidden;
    }

    .product-modal .modal-body {
        padding: 20px;
    }

    .product-table th {
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

    /* Order badge styling */
    .order-badge {
        background-color: #6c757d;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Responsive adjustments */
    @media (max-width: 767px) {
        .product-title {
            font-size: 2rem;
        }

        .product-modal .modal-body {
            padding: 15px;
        }

        #productsTable tbody td {
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

        <h3 class="mb-3 text-center product-title"><i class="bi bi-box-seam"></i> All Products</h3>

        <div class="table-responsive">

            <table id="productsTable" class="table table-striped table-hover w-100">
                <thead class="custom-table-header">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Order</th>
                        <th class="text-center">Image</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Featured</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td class="text-center">
                                <span class="order-badge">
                                    <i class="bi bi-sort-numeric-down me-1"></i>{{ $product->index_no ?? '999' }}
                                </span>
                            </td>
                            <td><img src="{{ asset($product->image) }}" width="50" height="50" class="rounded" alt="{{ $product->name }}"></td>

                            <td><strong>{{ $product->name }}</strong></td>
                            <td>
                                @if ($product->featured)
                                    <span class="badge" style="background-color: #4154f1;"><i class="bi bi-star-fill"></i> Featured</span>
                                @else
                                    <span class="badge bg-secondary">Normal</span>
                                @endif
                            </td>
                            <td>
                                @if ($product->status == 1)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Disabled</span>
                                @endif
                            </td>
                            <td>
                                <button style="background-color: #4154f1; color: white; " class="btn btn-sm action-btn view-product" data-id="{{ $product->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="{{ route('admin.products.edit', $product->slug) }}" class="btn btn-warning btn-sm action-btn">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a class="btn btn-danger btn-sm action-btn delete-product" data-id="{{ $product->id }}">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content product-modal">
            <div class="modal-header custom-modal-header">
                <h5 class="modal-title" id="productModalLabel"><i class="bi bi-info-circle-fill me-2"></i>Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered product-table">
                    <tbody>
                        <tr><th>Name</th><td id="modalProductName"></td></tr>
                        <tr><th>Display Order</th><td id="modalProductOrder"></td></tr>
                        <tr><th>Slug</th><td id="modalProductSlug"></td></tr>
                        <tr><th>Price</th><td id="modalProductPrice"></td></tr>
                        <tr><th>Discount Toggle</th><td id="modalProductDiscountToggle"></td></tr>
                        <tr><th>Discounted Price</th><td id="modalProductDiscountedPrice"></td></tr>
                        <tr><th>Short Description</th><td id="modalProductDescription"></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- DataTables and Export Buttons -->

<script>
    $(document).ready(function() {
        $('#productsTable').DataTable({
            responsive: true,
            paging: true,
            pageLength: 10,
            order: [[1, 'asc']], // Sort by display order column by default
            dom: '<"row"<"col-md-6"l><"col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-5"i><"col-sm-7"p>><"row"<"col-sm-12"B>>',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> Export to Excel',
                    className: 'btn btn-success',
                    exportOptions: {
                        columns: [0, 1, 3, 4, 5]
                    }
                }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search products...",
                lengthMenu: "_MENU_ products per page",
                info: "Showing _START_ to _END_ of _TOTAL_ products",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            }
        });

        // View Product
        $('.view-product').on('click', function() {
            var productId = $(this).data('id');
            $.ajax({
                url: "{{ url('admin/products/details') }}/" + productId,
                type: 'GET',
                success: function(data) {
                    $('#modalProductName').text(data.name);
                    $('#modalProductOrder').text(data.index_no ? data.index_no : 'Not set (999)');
                    $('#modalProductSlug').text(data.slug);
                    $('#modalProductPrice').text('$' + data.price);
                    $('#modalProductDiscountToggle').text(data.discount_toggle ? 'Enabled' : 'Disabled');
                    
                    var discountedPrice = data.discount_toggle && data.discount ? 
                        '$' + (data.price - data.discount).toFixed(2) : 
                        'No discount';
                    
                    $('#modalProductDiscountedPrice').text(discountedPrice);
                    $('#modalProductDescription').text(data.short_description);
                    
                    $('#productModal').modal('show');
                }
            });
        });

        // Delete Product
        $('.delete-product').on('click', function(e) {
            e.preventDefault();
            
            var productId = $(this).data('id');
            
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
                    $.ajax({
                        url: "{{ url('admin/products/delete') }}/" + productId,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonColor: '#4154f1'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonColor: '#4154f1'
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
