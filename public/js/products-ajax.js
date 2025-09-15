$(document).ready(function() {
    // Update Rates (reuse profit endpoint)
    $(document).on('click', '#updateRatesBtn', function() {
        const btn = $(this);
        const url = btn.data('url');

        if (!url) {
            Swal.fire({ icon: 'error', title: 'Error!', text: 'Update URL not found.', confirmButtonColor: '#4154f1' });
            return;
        }

        Swal.fire({
            text: 'Are you sure to update the purchase rate of all products as current rate?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes, update',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#4154f1',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Updating...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(resp) {
                        if (resp && resp.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: 'Rate updated successfully!', confirmButtonColor: '#4154f1' })
                                .then(() => { location.reload(); });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: (resp && resp.message) ? resp.message : 'Please try again!', confirmButtonColor: '#4154f1' });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error!', text: 'Request failed. Please try again!', confirmButtonColor: '#4154f1' });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    });
    $(document).on('change', '#is_dippable', function() {
        if ($(this).val() == '1') {
            $('.tank-field').show();
        } else {
            $('.tank-field').hide();
            $('#tank_id').val('');
        }
    });

    // Open Add Product Modal
    $('#addNewProductBtn').on('click', function() {
        $('#addProductForm')[0].reset();
        $('.invalid-feedback').text('');
        $('.tank-field').hide();
        $('#addProductModal').modal('show');
    });

    // Add Product Form Submission
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let $submitBtn = $('.add-product');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $submitBtn.prop('disabled', true);
                $submitBtn.find('.spinner-border').removeClass('d-none');
                $submitBtn.find('.submit-icon').addClass('d-none');
                $('.invalid-feedback').text('');
            },
            success: function(response) {
                if (response.success) {
                    $('#addProductModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            },
            complete: function() {
                $submitBtn.prop('disabled', false);
                $submitBtn.find('.spinner-border').addClass('d-none');
                $submitBtn.find('.submit-icon').removeClass('d-none');
            }
        });
    });

    // Edit Product
    $(document).on('click', '.edit-product', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let unit = $(this).data('unit');
        let current_purchase = $(this).data('current_purchase');
        let current_sale = $(this).data('current_sale');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_unit').val(unit);
        $('#edit_current_purchase').val(current_purchase);
        $('#edit_current_sale').val(current_sale);

        $('.invalid-feedback').text('');
        $('#editProductModal').modal('show');
    });

    $('#editProductForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let $submitBtn = $('.update-product');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $submitBtn.prop('disabled', true);
                $submitBtn.find('.spinner-border').removeClass('d-none');
                $submitBtn.find('.submit-icon').addClass('d-none');
                $('.invalid-feedback').text('');
            },
            success: function(response) {
                if (response.success) {
                    $('#editProductModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            },
            complete: function() {
                $submitBtn.prop('disabled', false);
                $submitBtn.find('.spinner-border').addClass('d-none');
                $submitBtn.find('.submit-icon').removeClass('d-none');
            }
        });
    });

    $(document).on('click', '.delete-product', function() {
        let productId = $(this).data('id');
        $('#delete_product_id').val(productId);
        $('#deleteConfirmModal').modal('show');
    });

    $(document).on('click', '#confirmDelete', function() {
        let productId = $('#delete_product_id').val();
        let $btn = $(this);

        $.ajax({
            url: `/products/${productId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $btn.prop('disabled', true);
                $btn.find('.spinner-border').removeClass('d-none');
                $btn.find('.submit-icon').addClass('d-none');
                $('.invalid-feedback').text('');
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteConfirmModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btn.find('.spinner-border').addClass('d-none');
                $btn.find('.submit-icon').removeClass('d-none');
            }
        });
    });
});
