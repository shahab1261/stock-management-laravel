$(document).ready(function() {
    $('#addNewIncomeBtn').on('click', function() {
        resetAddForm();
        $('#addIncomeModal').modal('show');
    });

    $('#saveBtn').on('click', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        const formData = $('#incomeForm').serialize();

        $.ajax({
            url: $('#incomeForm').attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#addIncomeModal').modal('hide');

                if(response.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Income added successfully',
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
                    text: 'Failed to add income. Please try again.',
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

        const incomeId = $(this).data('id');
        const incomeName = $(this).data('income-name');
        const incomeAmount = $(this).data('income-amount');

        $('#edit_income_id').val(incomeId);
        $('#edit_income_name').val(incomeName);
        $('#edit_income_amount').val(incomeAmount);

        $('#editIncomeModal').modal('show');
    });

    $('#updateBtn').on('click', function() {
        $('#editIncomeModal .is-invalid').removeClass('is-invalid');
        $('#editIncomeModal .invalid-feedback').text('');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        const formData = $('#editIncomeForm').serialize();

        $.ajax({
            url: $('#editIncomeForm').attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editIncomeModal').modal('hide');

                if(response.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message || 'Income updated successfully',
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
                    text: 'Failed to update income. Please try again.',
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
        const incomeId = $(this).data('id');
        $('#delete_income_id').val(incomeId);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        const incomeId = $('#delete_income_id').val();
        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: `/admin/incomes/delete/${incomeId}`,
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
                        text: response.message || 'Income deleted successfully',
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
                    text: 'Failed to delete income. Please try again.',
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
        $('#incomeForm')[0].reset();
        $('#incomeForm .is-invalid').removeClass('is-invalid');
        $('#incomeForm .invalid-feedback').text('');
    }

    function resetEditForm() {
        $('#editIncomeForm')[0].reset();
        $('#editIncomeModal .is-invalid').removeClass('is-invalid');
        $('#editIncomeModal .invalid-feedback').text('');
    }
});
