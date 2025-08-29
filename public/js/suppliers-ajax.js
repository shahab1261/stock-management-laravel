$(document).ready(function() {
    $('#addNewSupplierBtn').on('click', function() {
        $('#addSupplierModal').modal('show');
    });

    $('#addSupplierForm').on('submit', function(e) {
        e.preventDefault();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $submitBtn = $('.add_supplier');
        $submitBtn.prop('disabled', true);
        $submitBtn.find('.spinner-border').removeClass('d-none');
        $submitBtn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/suppliers/store',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#addSupplierModal').modal('hide');
                    $('#addSupplierForm')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Supplier added successfully',
                        confirmButtonColor: '#4154f1'
                    }).then(function() {
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
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '-error').show().text(value[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false);
                $submitBtn.find('.spinner-border').addClass('d-none');
                $submitBtn.find('.submit-icon').removeClass('d-none');
            }
        });
    });

    $(document).on('click', '.edit-supplier', function() {
        $('#editSupplierForm')[0].reset();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var supplier = $(this);
        $('#edit_id').val(supplier.data('id'));
        $('#edit_name').val(supplier.data('name'));
        $('#edit_supplier_type').val(supplier.data('supplier-type'));
        $('#edit_contact_person').val(supplier.data('contact-person'));
        $('#edit_item_type').val(supplier.data('item-type'));
        $('#edit_mobile_no').val(supplier.data('mobile'));
        $('#edit_email').val(supplier.data('email'));
        $('#edit_fax_no').val(supplier.data('fax'));
        $('#edit_ntn_no').val(supplier.data('ntn'));
        $('#edit_gst_no').val(supplier.data('gst'));
        $('#edit_balance').val(supplier.data('balance'));
        $('#edit_status').val(supplier.data('status'));
        $('#edit_address').val(supplier.data('address'));
        $('#edit_terms').val(supplier.data('terms'));

        $('#editSupplierModal').modal('show');
    });

    $('#editSupplierForm').on('submit', function(e) {
        e.preventDefault();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $submitBtn = $('.edit_supplier');
        $submitBtn.prop('disabled', true);
        $submitBtn.find('.spinner-border').removeClass('d-none');
        $submitBtn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/suppliers/update',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#editSupplierModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Supplier updated successfully',
                        confirmButtonColor: '#4154f1'
                    }).then(function() {
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
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#edit_' + key).addClass('is-invalid');
                        $('#edit-' + key + '-error').show().text(value[0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false);
                $submitBtn.find('.spinner-border').addClass('d-none');
                $submitBtn.find('.submit-icon').removeClass('d-none');
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.delete-supplier', function() {
        var supplierId = $(this).data('id');
        $('#delete_id').val(supplierId);
        $('#deleteSupplierModal').modal('show');
    });

    // Handle confirm delete button click
    $('#confirmDelete').on('click', function() {
        var supplierId = $('#delete_id').val();

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: `/suppliers/delete/${supplierId}`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteSupplierModal').modal('hide');
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'Supplier deleted successfully',
                        confirmButtonColor: '#4154f1'
                    }).then(function() {
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
            error: function() {
                $('#deleteSupplierModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to delete supplier. Please try again.',
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

    // Initialize DataTable if available
    if($.fn.DataTable) {
        $('#suppliersTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 10,
            order: [[0, 'asc']],
        });
    }
});
