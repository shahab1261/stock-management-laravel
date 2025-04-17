$(document).ready(function() {
    $('#addNewTankLariBtn').on('click', function() {
        $('#addTankLariModal').modal('show');
    });

    $('#submitAddForm').on('click', function() {
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/admin/tanklari/store',
            method: 'POST',
            data: $('#addTankLariForm').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#addTankLariModal').modal('hide');
                    $('#addTankLariForm')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    }).then(function() {
                        location.reload();
                    });
                } else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr, error) {
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

    $(document).on('click', '.edit-tank-lari', function() {
        $('#editTankLariForm')[0].reset();
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var tankLari = $(this);
        $('#editTankLariForm #edit_tid').val(tankLari.data('id'));
        $('#editTankLariForm #edit_larry_name').val(tankLari.data('name'));
        $('#editTankLariForm #edit_customer_id').val(tankLari.data('customer'));
        $('#editTankLariForm #edit_chamber_dip_one').val(tankLari.data('chamberDipOne'));
        $('#editTankLariForm #edit_chamber_capacity_one').val(tankLari.data('chamberCapacityOne'));
        $('#editTankLariForm #edit_chamber_dip_two').val(tankLari.data('chamberDipTwo'));
        $('#editTankLariForm #edit_chamber_capacity_two').val(tankLari.data('chamberCapacityTwo'));
        $('#editTankLariForm #edit_chamber_dip_three').val(tankLari.data('chamberDipThree'));
        $('#editTankLariForm #edit_chamber_capacity_three').val(tankLari.data('chamberCapacityThree'));
        $('#editTankLariForm #edit_chamber_dip_four').val(tankLari.data('chamberDipFour'));
        $('#editTankLariForm #edit_chamber_capacity_four').val(tankLari.data('chamberCapacityFour'));

        $('#editTankLariModal').modal('show');
    });

    $('#submitEditForm').on('click', function() {
        $('.invalid-feedback').hide();
        $('.is-invalid').removeClass('is-invalid');

        var $btn = $(this);
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.submit-icon').addClass('d-none');

        $.ajax({
            url: '/admin/tanklari/update',
            method: 'POST',
            data: $('#editTankLariForm').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#editTankLariModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    }).then(function() {
                        location.reload();
                    });
                } else{
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

    $(document).on('click', '.delete-tank-lari', function() {
        var tankLariId = $(this).data('id');
        $('#delete_tanklari_id').val(tankLariId);
        $('#deleteModal').modal('show');
    });
    // Delete Tank Lari
    $('#confirmDeleteBtn').on('click', function() {
        var tankLariId = $('#delete_tanklari_id').val();

        $.ajax({
            url: '/admin/tanklari/delete/' + tankLariId,
            method: 'GET',
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
                } else{
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
                    text: 'Failed to delete Tank Lari. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

});
