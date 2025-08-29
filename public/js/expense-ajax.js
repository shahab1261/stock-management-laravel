$(document).ready(function() {
    // Show Add Expense Modal
    $('#addNewExpenseBtn').on('click', function() {
        resetAddForm();
        $('#addExpenseModal').modal('show');
    });

    // Save New Expense
    $('#saveBtn').on('click', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        const formData = $('#expenseForm').serialize();

        $.ajax({
            url: $('#expenseForm').attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#addExpenseModal').modal('hide');

                if(response.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Expense added successfully',
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
            error: function(xhr, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to add expense. Please try again.',
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

    $(document).on('click', '.edit-btn', function() {
        resetEditForm();

        const expenseId = $(this).data('id');
        const expenseName = $(this).data('expense-name');
        const expenseAmount = $(this).data('expense-amount');

        $('#edit_expense_id').val(expenseId);
        $('#edit_expense_name').val(expenseName);
        $('#edit_expense_amount').val(expenseAmount);

        $('#editExpenseModal').modal('show');
    });

    // Update Expense
    $('#updateBtn').on('click', function() {
        $('#editExpenseModal .is-invalid').removeClass('is-invalid');
        $('#editExpenseModal .invalid-feedback').text('');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        const formData = $('#editExpenseForm').serialize();

        $.ajax({
            url: $('#editExpenseForm').attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editExpenseModal').modal('hide');

                if(response.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message || 'Expense updated successfully',
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
            error: function(xhr, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update expense. Please try again.',
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

    $(document).on('click', '.delete-btn', function() {
        const expenseId = $(this).data('id');
        $('#delete_expense_id').val(expenseId);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        const expenseId = $('#delete_expense_id').val();
        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: `/expenses/delete/${expenseId}`,
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
                        text: response.message || 'Expense deleted successfully',
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
            error: function(xhr, error) {
                $('#deleteModal').modal('hide');

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete expense. Please try again.',
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

    function resetAddForm() {
        $('#expenseForm')[0].reset();
        $('#expenseForm .is-invalid').removeClass('is-invalid');
        $('#expenseForm .invalid-feedback').text('');
    }

    function resetEditForm() {
        $('#editExpenseForm')[0].reset();
        $('#editExpenseModal .is-invalid').removeClass('is-invalid');
        $('#editExpenseModal .invalid-feedback').text('');
    }
});
