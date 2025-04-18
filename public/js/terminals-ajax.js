$(document).ready(function() {

    // Show Add Terminal Modal
    $('#addNewTerminalBtn').on('click', function() {
        resetForm('#addTerminalForm');
        $('#addTerminalModal').modal('show');
    });

    // Show Edit Terminal Modal
    $(document).on('click', '.edit-terminal', function() {
        resetForm('#editTerminalForm');

        const id = $(this).data('id');
        const name = $(this).data('name');
        const address = $(this).data('address');
        const notes = $(this).data('notes');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_address').val(address);
        $('#edit_notes').val(notes);

        $('#editTerminalModal').modal('show');
    });

    // Show Delete Terminal Modal
    $(document).on('click', '.delete-terminal', function() {
        const id = $(this).data('id');
        $('#delete_id').val(id);
        $('#deleteTerminalModal').modal('show');
    });

    // Add Terminal Form Submission
    $('#addTerminalForm').on('submit', function(e) {
        e.preventDefault();

        // Show spinner and disable button
        const submitBtn = $('.addTerminalBtn');
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
                    $('#addTerminalModal').modal('hide');
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

    // Edit Terminal Form Submission
    $('#editTerminalForm').on('submit', function(e) {
        e.preventDefault();

        // Show spinner and disable button
        const submitBtn = $('.editTerminalBtn');
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
                    $('#editTerminalModal').modal('hide');
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

    // Delete Terminal Confirmation
    $('#confirmDelete').on('click', function() {
        const id = $('#delete_id').val();

        // Show spinner and disable button
        const deleteBtn = $(this);
        deleteBtn.prop('disabled', true);
        deleteBtn.find('.spinner-border').removeClass('d-none');
        deleteBtn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: `/admin/terminals/delete/${id}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Hide modal and show success message
                    $('#deleteTerminalModal').modal('hide');
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
