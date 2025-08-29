$(document).ready(function() {
    $('#addNewDriverBtn').on('click', function() {
        resetAddForm();
        $('#addDriverModal').modal('show');
    });

    $('#saveBtn').on('click', function() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        const formData = $('#driverForm').serialize();

        $.ajax({
            url: $('#driverForm').attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success == true) {
                    $('#addDriverModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Driver added successfully',
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
                    text: 'Failed to add driver. Please try again.',
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

        const driverId = $(this).data('id');
        const driverType = $(this).data('driver-type');
        const driverName = $(this).data('driver-name');
        const firstMobile = $(this).data('first-mobile');
        const secondMobile = $(this).data('second-mobile');
        const cnic = $(this).data('cnic');
        const vehicle = $(this).data('vehicle');
        const city = $(this).data('city');
        const address = $(this).data('address');
        const reference = $(this).data('reference');

        $('#edit_driver_id').val(driverId);
        $('#edit_driver_type').val(driverType);
        $('#edit_driver_name').val(driverName);
        $('#edit_first_mobile_no').val(firstMobile);
        $('#edit_second_mobile_no').val(secondMobile);
        $('#edit_cnic').val(cnic);
        $('#edit_vehicle_no').val(vehicle);
        $('#edit_city').val(city);
        $('#edit_address').val(address);
        $('#edit_reference').val(reference);

        $('#editDriverModal').modal('show');
    });

    $('#updateBtn').on('click', function() {
        $('#editDriverModal .is-invalid').removeClass('is-invalid');
        $('#editDriverModal .invalid-feedback').text('');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        const formData = $('#editDriverForm').serialize();

        $.ajax({
            url: $('#editDriverForm').attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#editDriverModal').modal('hide');

                if(response.success == true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message || 'Driver updated successfully',
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update driver. Please try again.',
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
        const driverId = $(this).data('id');
        $('#delete_driver_id').val(driverId);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        const driverId = $('#delete_driver_id').val();

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: `/drivers/delete/${driverId}`,
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
                        text: response.message || 'Driver deleted successfully',
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
                    text: 'Failed to delete driver. Please try again.',
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
        $('#driverForm')[0].reset();
        $('#driverForm .is-invalid').removeClass('is-invalid');
        $('#driverForm .invalid-feedback').text('');
    }

    function resetEditForm() {
        $('#editDriverForm')[0].reset();
        $('#editDriverModal .is-invalid').removeClass('is-invalid');
        $('#editDriverModal .invalid-feedback').text('');
    }
});
