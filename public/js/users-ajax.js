$(document).ready(function() {
    $('#addNewUserBtn').on('click', function() {
        $('#addUserModal').modal('show');
    });

    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $submitBtn = $('.addUserbtn');
        $submitBtn.prop('disabled', true);
        $submitBtn.find('.spinner-border').removeClass('d-none');
        $submitBtn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/users/store',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#addUserModal').modal('hide');
                    $('#addUserForm')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'User added successfully',
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

    $(document).on('click', '.edit-user', function() {
        $('#editUserForm')[0].reset();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var user = $(this);
        $('#edit_id').val(user.data('id'));
        $('#edit_name').val(user.data('name'));
        $('#edit_email').val(user.data('email'));
        $('#edit_phone').val(user.data('phone'));
        $('#edit_bank_account_number').val(user.data('bank-account'));
        $('#edit_status').val(user.data('status'));
        $('#edit_user_type').val(user.data('user-type'));
        $('#edit_address').val(user.data('address'));
        $('#edit_notes').val(user.data('notes'));

        $('#editUserModal').modal('show');
    });

    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $submitBtn = $('.editUserbtn');
        $submitBtn.prop('disabled', true);
        $submitBtn.find('.spinner-border').removeClass('d-none');
        $submitBtn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/users/update',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#editUserModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'User updated successfully',
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

    // Password show/hide toggles
    $(document).on('click', '.toggle-password', function() {
        var input = $($(this).data('target'));
        var icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    // Handle delete button click
    $(document).on('click', '.delete-user', function() {
        var userId = $(this).data('id');
        $('#delete_id').val(userId);
        $('#deleteUserModal').modal('show');
    });

    // Handle confirm delete button click
    $('#confirmDelete').on('click', function() {
        var userId = $('#delete_id').val();

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: `/users/delete/${userId}`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteUserModal').modal('hide');
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'User deleted successfully',
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
                $('#deleteUserModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to delete user. Please try again.',
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
        $('#usersTable').DataTable({
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
