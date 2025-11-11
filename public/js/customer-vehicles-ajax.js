$(document).ready(function() {
    $('#addNewCustomerVehicleBtn').on('click', function() {
        $('#addCustomerVehicleModal').modal('show');
    });

    $('#submitAddForm').on('click', function() {
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/customer-vehicles/store',
            method: 'POST',
            data: $('#addCustomerVehicleForm').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#addCustomerVehicleModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
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
                        // Handle both customer vehicle and driver fields
                        var fieldId = key;
                        $('#' + fieldId).addClass('is-invalid');
                        $('#' + fieldId + '-error').html(value[0]).show();
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
                $btn.prop('disabled', false);
                $btn.find('.spinner-border').addClass('d-none');
                $btn.find('.submit-icon').removeClass('d-none');
            }
        });
    });

    $(document).on('click', '.edit-customer-vehicle', function() {
        $('#editCustomerVehicleForm')[0].reset();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var customerVehicle = $(this);
        $('#editCustomerVehicleForm #edit_id').val(customerVehicle.data('id'));
        $('#editCustomerVehicleForm #edit_larry_name').val(customerVehicle.data('name'));
        $('#editCustomerVehicleForm #edit_customer_id').val(customerVehicle.data('customer'));

        $('#editCustomerVehicleModal').modal('show');
    });

    $('#submitEditForm').on('click', function() {
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/customer-vehicles/update',
            method: 'POST',
            data: $('#editCustomerVehicleForm').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#editCustomerVehicleModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
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
                        $('#edit_' + key + '-error').html(value[0]).show();
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
                $btn.prop('disabled', false);
                $btn.find('.spinner-border').addClass('d-none');
                $btn.find('.submit-icon').removeClass('d-none');
            }
        });
    });

    $(document).on('click', '.delete-customer-vehicle', function() {
        var customerVehicleId = $(this).data('id');
        var customerVehicleName = $(this).data('name');
        $('#delete_customer_vehicle_id').val(customerVehicleId);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        var customerVehicleId = $('#delete_customer_vehicle_id').val();

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/customer-vehicles/delete/' + customerVehicleId,
            method: 'DELETE',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message,
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
                $('#deleteModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to delete customer vehicle. Please try again.',
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
});
