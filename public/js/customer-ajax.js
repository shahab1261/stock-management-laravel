$(document).ready(function() {
    $('#addNewCustomerBtn').on('click', function() {
        resetAddForm();
        $('#addCustomerModal').modal('show');
    });

    $(document).on('click', '.edit-btn', function() {
        resetEditForm();

        const customerId = $(this).data('id');
        const customerName = $(this).data('name');
        const companyName = $(this).data('company');
        const customerEmail = $(this).data('email');
        const customerPhone = $(this).data('phone');
        const customerAddress = $(this).data('address');
        const creditLimit = $(this).data('credit');
        const bankAccount = $(this).data('account');
        const customerNotes = $(this).data('notes');
        const customerStatus = $(this).data('status');

        $('#editCustomerModal #customer_id').val(customerId);
        $('#editCustomerModal #name').val(customerName);
        $('#editCustomerModal #company_name').val(companyName);
        $('#editCustomerModal #email').val(customerEmail);
        $('#editCustomerModal #phone').val(customerPhone);
        $('#editCustomerModal #address').val(customerAddress);
        $('#editCustomerModal #credit_limit').val(creditLimit);
        $('#editCustomerModal #bank_account_number').val(bankAccount);
        $('#editCustomerModal #notes').val(customerNotes);

        if (customerStatus == 1) {
            $('#editCustomerModal #editStatusActive').prop('checked', true);
        } else {
            $('#editCustomerModal #editStatusInactive').prop('checked', true);
        }

        $('#editCustomerModal').modal('show');
    });

    $(document).on('click', '.delete-btn', function() {
        const customerId = $(this).data('id');
        $('#delete_customer_id').val(customerId);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        const customerId = $('#delete_customer_id').val();

        $.ajax({
            url: `/admin/customers/delete/${customerId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');

                if(response.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'Customer has been deleted successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr) {
                $('#deleteModal').modal('hide');

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete customer. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    $('#addCustomerModal #saveBtn').on('click', function() {
        $('#addCustomerModal .is-invalid').removeClass('is-invalid');
        $('#addCustomerModal .invalid-feedback').text('');

        const formData = $('#addCustomerModal form').serialize();

        $.ajax({
            url: $('#addCustomerModal form').attr('action'),
            type: $('#addCustomerModal form').attr('method'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#addCustomerModal').modal('hide');

                if(response.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Created!',
                        text: response.message || 'Customer has been created successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        $(`#addCustomerModal #${field}`).addClass('is-invalid');
                        $(`#addCustomerModal #${field}-error`).text(errors[field][0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to create customer. Please try again.',
                        confirmButtonColor: '#4154f1'
                    });
                }
            }
        });
    });

    $('#editCustomerModal #updateBtn').on('click', function() {
        $('#editCustomerModal .is-invalid').removeClass('is-invalid');
        $('#editCustomerModal .invalid-feedback').text('');

        const formData = $('#editCustomerModal form').serialize();

        $.ajax({
            url: $('#editCustomerModal form').attr('action'),
            type: $('#editCustomerModal form').attr('method'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editCustomerModal').modal('hide');

                if(response.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message || 'Customer has been updated successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        $(`#editCustomerModal #${field}`).addClass('is-invalid');
                        $(`#editCustomerModal #${field}-error`).text(errors[field][0]);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update customer. Please try again.',
                        confirmButtonColor: '#4154f1'
                    });
                }
            }
        });
    });

    function resetAddForm() {
        $('#addCustomerModal form')[0].reset();
        $('#addCustomerModal .is-invalid').removeClass('is-invalid');
        $('#addCustomerModal .invalid-feedback').text('');
    }

    function resetEditForm() {
        $('#editCustomerModal form')[0].reset();
        $('#editCustomerModal #customer_id').val('');
        $('#editCustomerModal .is-invalid').removeClass('is-invalid');
        $('#editCustomerModal .invalid-feedback').text('');
    }
});
