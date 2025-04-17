$(document).ready(function() {
    $('#addNewBankBtn').on('click', function() {
        resetAddForm();
        $('#addBankModal').modal('show');
    });

    $(document).on('click', '.edit-btn', function() {
        resetEditForm();

        const bankId = $(this).data('id');
        const bankName = $(this).data('name');
        const bankAccountNumber = $(this).data('acc');
        const bankCode = $(this).data('bank-code');
        const bankAddress = $(this).data('address');
        const bankNotes = $(this).data('notes');
        const bankStatus = $(this).data('status');

        $('#editBankModal #bank_id').val(bankId);
        $('#editBankModal #name').val(bankName);
        $('#editBankModal #account_number').val(bankAccountNumber);
        $('#editBankModal #bank_code').val(bankCode);
        $('#editBankModal #address').val(bankAddress);
        $('#editBankModal #notes').val(bankNotes);

        if (bankStatus == 1) {
            $('#editBankModal #statusActive').prop('checked', true);
        } else {
            $('#editBankModal #statusInactive').prop('checked', true);
        }

        $('#editBankModal').modal('show');
    });

    $(document).on('click', '.delete-btn', function() {
        const bankId = $(this).data('id');
        $('#delete_bank_id').val(bankId);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        const bankId = $('#delete_bank_id').val();

        $.ajax({
            url: `/admin/banks/delete/${bankId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');

                if(response.success == "true" || response.success == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Created!',
                        text: response.message || 'Bank has been created successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else{
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
                    text: 'Failed to delete bank. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    $('#addBankModal #saveBtn').on('click', function() {
        $('#addBankModal .is-invalid').removeClass('is-invalid');
        $('#addBankModal .invalid-feedback').text('');

        const formData = $('#addBankModal form').serialize();

        $.ajax({
            url: $('#addBankModal form').attr('action'),
            type: $('#addBankModal form').attr('method'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#addBankModal').modal('hide');
                if(response.success == "true" || response.success == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Created!',
                        text: response.message || 'Bank has been created successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(error, xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text:  'Failed to create bank. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    $('#editBankModal #saveBtn').on('click', function() {
        $('#editBankModal .is-invalid').removeClass('is-invalid');
        $('#editBankModal .invalid-feedback').text('');

        const formData = $('#editBankModal form').serialize();

        $.ajax({
            url: $('#editBankModal form').attr('action'),
            type: $('#editBankModal form').attr('method'),
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editBankModal').modal('hide');

                if(response.success == "true" || response.success == true){
                    Swal.fire({
                        icon: 'success',
                        title: 'Created!',
                        text: response.message || 'Bank has been created successfully.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text:  'Failed to create bank. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    function resetAddForm() {
        $('#addBankModal form')[0].reset();
        $('#addBankModal .is-invalid').removeClass('is-invalid');
        $('#addBankModal .invalid-feedback').text('');
    }

    function resetEditForm() {
        $('#editBankModal form')[0].reset();
        $('#editBankModal #bank_id').val('');
        $('#editBankModal .is-invalid').removeClass('is-invalid');
        $('#editBankModal .invalid-feedback').text('');
    }
});
