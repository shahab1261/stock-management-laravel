$(document).ready(function() {
    // Show Add Employee Modal
    $('#addNewEmployeeBtn').on('click', function() {
        resetForm('#addEmployeeForm');
        $('#addEmployeeModal').modal('show');
    });

    // Show Edit Employee Modal
    $(document).on('click', '.edit-employee', function() {
        resetForm('#editEmployeeForm');

        const id = $(this).data('id');
        const name = $(this).data('name');
        const email = $(this).data('email');
        const phone = $(this).data('phone');
        const bankAccount = $(this).data('bank-account');
        const address = $(this).data('address');
        const notes = $(this).data('notes');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_email').val(email);
        $('#edit_phone').val(phone);
        $('#edit_bank_account_number').val(bankAccount);
        $('#edit_address').val(address);
        $('#edit_notes').val(notes);

        $('#editEmployeeModal').modal('show');
    });

    // Show Delete Employee Modal
    $(document).on('click', '.delete-employee', function() {
        const id = $(this).data('id');
        $('#delete_id').val(id);
        $('#deleteEmployeeModal').modal('show');
    });

    // Add Employee Form Submission
    $('#addEmployeeForm').on('submit', function(e) {
        e.preventDefault();

        // Show spinner and disable button
        const submitBtn = $('.addEmployeeBtn');
        submitBtn.prop('disabled', true);
        submitBtn.find('.spinner-border').removeClass('d-none');
        submitBtn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Hide modal and show success message
                    $('#addEmployeeModal').modal('hide');
                    showAlert('success', 'Success', response.message);

                    // Reload page after a short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', 'Error', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '-error').text(value[0]).show();
                    });
                } else {
                    showAlert('error', 'Error', 'Something went wrong. Please try again.');
                }
            },
            complete: function() {
                // Hide spinner and enable button
                submitBtn.prop('disabled', false);
                submitBtn.find('.spinner-border').addClass('d-none');
                submitBtn.find('.submit-icon').removeClass('d-none');
            }
        });
    });

    // Edit Employee Form Submission
    $('#editEmployeeForm').on('submit', function(e) {
        e.preventDefault();

        // Show spinner and disable button
        const submitBtn = $('.editEmployeeBtn');
        submitBtn.prop('disabled', true);
        submitBtn.find('.spinner-border').removeClass('d-none');
        submitBtn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Hide modal and show success message
                    $('#editEmployeeModal').modal('hide');
                    showAlert('success', 'Success', response.message);

                    // Reload page after a short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', 'Error', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#edit_' + key).addClass('is-invalid');
                        $('#edit-' + key + '-error').text(value[0]).show();
                    });
                } else {
                    showAlert('error', 'Error', 'Something went wrong. Please try again.');
                }
            },
            complete: function() {
                // Hide spinner and enable button
                submitBtn.prop('disabled', false);
                submitBtn.find('.spinner-border').addClass('d-none');
                submitBtn.find('.submit-icon').removeClass('d-none');
            }
        });
    });

    // Delete Employee Confirmation
    $('#confirmDelete').on('click', function() {
        const id = $('#delete_id').val();

        // Show spinner and disable button
        const deleteBtn = $(this);
        deleteBtn.prop('disabled', true);
        deleteBtn.find('.spinner-border').removeClass('d-none');
        deleteBtn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: `/admin/employees/delete/${id}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Hide modal and show success message
                    $('#deleteEmployeeModal').modal('hide');
                    showAlert('success', 'Success', response.message);

                    // Reload page after a short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', 'Error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Error', 'Something went wrong. Please try again.');
            },
            complete: function() {
                // Hide spinner and enable button
                deleteBtn.prop('disabled', false);
                deleteBtn.find('.spinner-border').addClass('d-none');
                deleteBtn.find('.submit-icon').removeClass('d-none');
            }
        });
    });

    // Reset form and clear validation errors
    function resetForm(formId) {
        $(formId)[0].reset();
        $(formId + ' .is-invalid').removeClass('is-invalid');
        $(formId + ' .invalid-feedback').text('');
    }

    // Show SweetAlert
    function showAlert(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            text: text,
            confirmButtonColor: '#4154f1',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    }
});
