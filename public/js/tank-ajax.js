$(document).ready(function() {
    $('#addNewTankBtn').on('click', function() {
        $('#addTankModal').modal('show');
    });

    $('#addTankForm').on('submit', function(e) {
        e.preventDefault();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $('.add-tank');
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#addTankModal').modal('hide');
                    $('#addTankForm')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Tank added successfully',
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

    $(document).on('click', '.edit-tank', function() {
        $('#editTankForm')[0].reset();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var tank = $(this);
        $('#edit_id').val(tank.data('id'));
        $('#edit_tank_name').val(tank.data('tank_name'));
        $('#edit_tank_limit').val(tank.data('tank_limit'));
        $('#edit_opening_stock').val(tank.data('opening_stock'));
        $('#edit_is_dippable').val(tank.data('is_dippable'));
        $('#edit_cost_price').val(tank.data('cost_price'));
        $('#edit_sales_price').val(tank.data('sales_price'));
        $('#edit_ob_date').val(tank.data('ob_date'));
        $('#edit_product_id').val(tank.data('product_id'));
        $('#edit_notes').val(tank.data('notes'));

        $('#editTankModal').modal('show');
    });

    $('#editTankForm').on('submit', function(e) {
        e.preventDefault();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $('.edit-tank-btn');
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#editTankModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Tank updated successfully',
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

    $(document).on('click', '.delete-tank', function() {
        var tankId = $(this).data('id');
        $('#delete_id').val(tankId);
        $('#deleteTankModal').modal('show');
    });

    $('#confirmDelete').on('click', function() {
        var tankId = $('#delete_id').val();

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: `/tanks/delete/${tankId}`,
            type: 'DELETE',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteTankModal').modal('hide');
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'Tank deleted successfully',
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
                $('#deleteTankModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to delete tank. Please try again.',
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

    // View Dip Charts functionality
    $(document).on('click', '.view-dip-charts', function() {
        var tankId = $(this).data('id');
        window.location.href = `/tanks/${tankId}/dip-charts-page`;
    });

    // Add Dip Charts functionality (CSV Upload)
    $(document).on('click', '.add-dip-charts', function() {
        var tankId = $(this).data('id');
        var tankName = $(this).data('name');
        
        $('#upload_tank_id').val(tankId);
        $('#uploadTankName').text(tankName);
        $('#uploadDipChartsForm')[0].reset();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');
        $('#uploadDipChartsModal').modal('show');
    });

    // Handle CSV file upload
    $('#uploadDipChartsBtn').on('click', function() {
        var form = $('#uploadDipChartsForm')[0];
        var formData = new FormData(form);
        var $btn = $(this);
        
        // Validate file
        var fileInput = $('#csv_file')[0];
        if (!fileInput.files || !fileInput.files[0]) {
            $('#csv_file').addClass('is-invalid');
            $('#csv_file-error').text('Please select a CSV file').show();
            return;
        }

        // Validate file extension
        var fileName = fileInput.files[0].name;
        var fileExtension = fileName.split('.').pop().toLowerCase();
        if (fileExtension !== 'csv') {
            $('#csv_file').addClass('is-invalid');
            $('#csv_file-error').text('Please select a valid CSV file').show();
            return;
        }

        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        // Show loading state with SweetAlert
        Swal.fire({
            title: 'Uploading...',
            text: 'Please wait while we process and import the dip charts from your CSV file.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/tanks/upload-dip-charts',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#uploadDipChartsModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Dip charts uploaded successfully',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4154f1'
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to upload dip charts',
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr) {
                var errorMessage = 'Failed to upload dip charts. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Handle validation errors
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '-error').text(value[0]).show();
                    });
                    errorMessage = 'Please fix the errors and try again.';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });
});
