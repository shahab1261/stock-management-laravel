$(document).ready(function() {
    $('#addNewNozzleBtn').on('click', function() {
        $('#addNozzleModal').modal('show');
    });

    $('#addNozzleForm').on('submit', function(e) {
        e.preventDefault();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $('.add-nozzle');
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
                    $('#addNozzleModal').modal('hide');
                    $('#addNozzleForm')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Nozzle added successfully',
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

    $(document).on('click', '.edit-nozzle', function() {
        $('#editNozzleForm')[0].reset();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var nozzle = $(this);
        $('#edit_id').val(nozzle.data('id'));
        $('#edit_name').val(nozzle.data('name'));
        $('#edit_opening_reading').val(nozzle.data('opening_reading'));
        $('#edit_product_id').val(nozzle.data('product_id'));
        $('#edit_notes').val(nozzle.data('notes'));

        var selectedTankId = nozzle.data('tank_id');

        var tanks = nozzle.data('tanks');
        var tankOptions = `<option value="${tanks.id}" selected>${tanks.tank_name}</option>`;

        $('#edit_tank_id').html(tankOptions);

        $('#editNozzleModal').modal('show');
    });

    $('#editNozzleForm').on('submit', function(e) {
        e.preventDefault();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $('.editNozzle');
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
                    $('#editNozzleModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Nozzle updated successfully',
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

    $(document).on('click', '.delete-nozzle', function() {
        var nozzleId = $(this).data('id');
        $('#delete_id').val(nozzleId);
        $('#deleteNozzleModal').modal('show');
    });

    $('#confirmDelete').on('click', function() {
        var nozzleId = $('#delete_id').val();

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: `/admin/nozzles/delete/${nozzleId}`,
            type: 'DELETE',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteNozzleModal').modal('hide');
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message || 'Nozzle deleted successfully',
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
                $('#deleteNozzleModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to delete nozzle. Please try again.',
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

    $("#product_id").on('change', function(){
        var productId = $(this).val();
        $.ajax({
            url: `/admin/products/${productId}/tank`,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let optionsHtml = '<option value="">Select Tank</option>';
                response.tank.forEach(tank => {
                    optionsHtml += `<option value="${tank.id}">${tank.tank_name}</option>`;
                });
                $("#tank_id").html(optionsHtml);
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    $("#edit_product_id").on('change', function(){
        var productId = $(this).val();
        $.ajax({
            url: `/admin/products/${productId}/tank`,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                let optionsHtml = '<option value="">Select Tank</option>';
                response.tank.forEach(tank => {
                    optionsHtml += `<option value="${tank.id}">${tank.tank_name}</option>`;
                });
                $("#edit_tank_id").html(optionsHtml);
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });
});
