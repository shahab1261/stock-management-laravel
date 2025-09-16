$(document).ready(function() {
    // Initialize DataTables
    if ($('#rolesTable').length) {
        $('#rolesTable').DataTable({
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: "Search roles...",
                lengthMenu: "Show _MENU_ roles per page",
                info: "Showing _START_ to _END_ of _TOTAL_ roles",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }

    if ($('#usersTable').length) {
        $('#usersTable').DataTable({
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: "Search users...",
                lengthMenu: "Show _MENU_ users per page",
                info: "Showing _START_ to _END_ of _TOTAL_ users",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }

    // Add New Role Button
    $('#addNewRoleBtn').on('click', function() {
        $('#addRoleModal').modal('show');
        resetAddRoleForm();
    });

    // Edit Role Button
    $(document).on('click', '.edit-role', function() {
        const roleId = $(this).data('id');
        const roleName = $(this).data('name');
        const permissions = $(this).data('permissions');

        $('#edit_role_id').val(roleId);
        $('#edit_role_name').val(roleName);

        // Reset all checkboxes
        $('.edit-permission-checkbox').prop('checked', false);
        $('.edit-group-select').prop('checked', false);

        // Check permissions
        if (permissions && permissions.length > 0) {
            permissions.forEach(function(permission) {
                $(`input[value="${permission}"].edit-permission-checkbox`).prop('checked', true);
            });
        }

        // Update group checkboxes
        updateGroupCheckboxes('.edit-group-select', '.edit-permission-checkbox');

        $('#editRoleModal').modal('show');
    });

    // Delete Role Button
    $(document).on('click', '.delete-role', function() {
        const roleId = $(this).data('id');
        $('#delete_role_id').val(roleId);
        $('#deleteRoleModal').modal('show');
    });

    // Assign Role Button
    $(document).on('click', '.assign-role', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const currentRole = $(this).data('current-role');

        $('#assign_user_id').val(userId);
        $('#assign_user_name').val(userName);
        $('#assign_current_role').val(currentRole);
        $('#assign_role_id').val('');

        $('#assignRoleModal').modal('show');
    });

    // Add Role Form Submit
    $('#addRoleForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = $('.addRoleBtn');
        const spinner = submitBtn.find('.spinner-border');
        const icon = submitBtn.find('.submit-icon');

        // Show loading state
        spinner.removeClass('d-none');
        icon.addClass('d-none');
        submitBtn.prop('disabled', true);

        // Clear previous errors
        clearErrors();

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    displayErrors(errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Something went wrong!'
                    });
                }
            },
            complete: function() {
                // Hide loading state
                spinner.addClass('d-none');
                icon.removeClass('d-none');
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Edit Role Form Submit
    $('#editRoleForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = $('.editRoleBtn');
        const spinner = submitBtn.find('.spinner-border');
        const icon = submitBtn.find('.submit-icon');

        // Show loading state
        spinner.removeClass('d-none');
        icon.addClass('d-none');
        submitBtn.prop('disabled', true);

        // Clear previous errors
        clearErrors();

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    displayErrors(errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Something went wrong!'
                    });
                }
            },
            complete: function() {
                // Hide loading state
                spinner.addClass('d-none');
                icon.removeClass('d-none');
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Assign Role Form Submit
    $('#assignRoleForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = $('.assignRoleBtn');
        const spinner = submitBtn.find('.spinner-border');
        const icon = submitBtn.find('.submit-icon');

        // Show loading state
        spinner.removeClass('d-none');
        icon.addClass('d-none');
        submitBtn.prop('disabled', true);

        // Clear previous errors
        clearErrors();

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    $('#assignRoleModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.reload();
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    displayErrors(errors);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Something went wrong!'
                    });
                }
            },
            complete: function() {
                // Hide loading state
                spinner.addClass('d-none');
                icon.removeClass('d-none');
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Confirm Delete Role
    $('#confirmDeleteRole').on('click', function() {
        const roleId = $('#delete_role_id').val();
        const submitBtn = $(this);
        const spinner = submitBtn.find('.spinner-border');
        const icon = submitBtn.find('.submit-icon');

        // Show loading state
        spinner.removeClass('d-none');
        icon.addClass('d-none');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: `/roles/delete/${roleId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Something went wrong!'
                });
            },
            complete: function() {
                // Hide loading state
                spinner.addClass('d-none');
                icon.removeClass('d-none');
                submitBtn.prop('disabled', false);
                $('#deleteRoleModal').modal('hide');
            }
        });
    });

    // Group checkbox functionality for add role
    $('.group-select').on('change', function() {
        const groupName = $(this).data('group');
        const isChecked = $(this).is(':checked');

        // Find all permission checkboxes in this group
        const card = $(this).closest('.card');
        card.find('.permission-checkbox').prop('checked', isChecked);
    });

    // Group checkbox functionality for edit role
    $('.edit-group-select').on('change', function() {
        const groupName = $(this).data('group');
        const isChecked = $(this).is(':checked');

        // Find all permission checkboxes in this group
        const card = $(this).closest('.card');
        card.find('.edit-permission-checkbox').prop('checked', isChecked);
    });

    // Individual permission checkbox change for add role
    $(document).on('change', '.permission-checkbox', function() {
        updateGroupCheckboxes('.group-select', '.permission-checkbox');
    });

    // Individual permission checkbox change for edit role
    $(document).on('change', '.edit-permission-checkbox', function() {
        updateGroupCheckboxes('.edit-group-select', '.edit-permission-checkbox');
    });

    // Helper function to update group checkboxes
    function updateGroupCheckboxes(groupSelector, permissionSelector) {
        $(groupSelector).each(function() {
            const card = $(this).closest('.card');
            const totalPermissions = card.find(permissionSelector).length;
            const checkedPermissions = card.find(permissionSelector + ':checked').length;

            if (checkedPermissions === 0) {
                $(this).prop('checked', false);
                $(this).prop('indeterminate', false);
            } else if (checkedPermissions === totalPermissions) {
                $(this).prop('checked', true);
                $(this).prop('indeterminate', false);
            } else {
                $(this).prop('checked', false);
                $(this).prop('indeterminate', true);
            }
        });
    }

    // Reset add role form
    function resetAddRoleForm() {
        $('#addRoleForm')[0].reset();
        $('.permission-checkbox').prop('checked', false);
        $('.group-select').prop('checked', false);
        clearErrors();
    }

    // Clear all error messages
    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    // Display validation errors
    function displayErrors(errors) {
        $.each(errors, function(field, messages) {
            const input = $(`[name="${field}"]`);
            const errorDiv = $(`#${field.replace(/\./g, '-')}-error`);

            if (input.length) {
                input.addClass('is-invalid');
            }

            if (errorDiv.length) {
                errorDiv.text(messages[0]);
            }
        });
    }

    // Modal close handlers
    $('#addRoleModal').on('hidden.bs.modal', function() {
        resetAddRoleForm();
    });

    $('#editRoleModal').on('hidden.bs.modal', function() {
        clearErrors();
    });

    $('#assignRoleModal').on('hidden.bs.modal', function() {
        clearErrors();
    });
});
