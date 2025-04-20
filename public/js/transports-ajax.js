$(document).ready(function() {
    $('#addNewTransportBtn').on('click', function() {
        $('#addTransportModal').modal('show');
    });

    $('#submitAddForm').on('click', function() {
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/admin/transports/store',
            method: 'POST',
            data: $('#addTransportForm').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#addTransportModal').modal('hide');
                    $('#addTransportForm')[0].reset();

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
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '-error').html(value[0]).show();
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

    $(document).on('click', '.edit-transport', function() {
        $('#editTransportForm')[0].reset();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var transport = $(this);
        $('#editTransportForm #edit_id').val(transport.data('id'));
        $('#editTransportForm #edit_larry_name').val(transport.data('name'));
        $('#editTransportForm #edit_driver_id').val(transport.data('driver'));
        $('#editTransportForm #edit_chamber_dip_one').val(transport.data('chamberDipOne'));
        $('#editTransportForm #edit_chamber_capacity_one').val(transport.data('chamberCapacityOne'));
        $('#editTransportForm #edit_chamber_dip_two').val(transport.data('chamberDipTwo'));
        $('#editTransportForm #edit_chamber_capacity_two').val(transport.data('chamberCapacityTwo'));
        $('#editTransportForm #edit_chamber_dip_three').val(transport.data('chamberDipThree'));
        $('#editTransportForm #edit_chamber_capacity_three').val(transport.data('chamberCapacityThree'));
        $('#editTransportForm #edit_chamber_dip_four').val(transport.data('chamberDipFour'));
        $('#editTransportForm #edit_chamber_capacity_four').val(transport.data('chamberCapacityFour'));

        $('#editTransportModal').modal('show');
    });

    $('#submitEditForm').on('click', function() {
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/admin/transports/update',
            method: 'POST',
            data: $('#editTransportForm').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#editTransportModal').modal('hide');
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

    $(document).on('click', '.delete-transport', function() {
        var transportId = $(this).data('id');
        var transportName = $(this).data('name');
        $('#delete_transport_id').val(transportId);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        var transportId = $('#delete_transport_id').val();

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/admin/transports/delete/' + transportId,
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
                    text: 'Failed to delete transport. Please try again.',
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
