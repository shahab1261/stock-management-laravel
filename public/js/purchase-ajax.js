$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $('#vehicle_chamber').on('change', function(){
        let tank_id = $(this).find('option:selected').val();

        $.ajax({
            url: "/admin/tank/chamber/data",
            method: 'POST',
            data: {
                _token: csrfToken,
                tank_id: tank_id,
            },
            success:function(response){
                if(response.data){
                    const data = response.data;

                    $('#chamber_capacity_one').val(data[0].chamber_capacity_one);
                    $('#chamber_dip_one').val(data[0].chamber_dip_one);
                    $('#chamber_capacity_two').val(data[0].chamber_capacity_two);
                    $('#chamber_dip_two').val(data[0].chamber_dip_two);
                    $('#chamber_capacity_three').val(data[0].chamber_capacity_three);
                    $('#chamber_dip_three').val(data[0].chamber_dip_three);
                    $('#chamber_capacity_four').val(data[0].chamber_capacity_four);
                    $('#chamber_dip_four').val(data[0].chamber_dip_four);
                    $('#driver_name').val(data[0].driver_id);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr, error){
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    // Handle product change event for updating tanks
    $('#product').on('change', function(){
        const selectedOption = $(this).find('option:selected');
        const productId = selectedOption.val();

        $.ajax({
            url: "/admin/product/tank/update",
            type: "POST",
            data: {
                _token: csrfToken,
                product_id: productId,
            },
            success: function(response) {
                if(response.tanks){
                    const tanks = response.tanks;
                    $('#tank_update').empty().append('<option selected disabled>Select Tank</option>');
                    $.each(tanks, function (key, value){
                        $("#tank_update").append(`<option value="${value.id}">${value.tank_name}</option>`);
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
            error: function(xhr, error){
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    // Handle product rate update
    $('#product').on('change', function(){
        const selectedOption = $(this).find('option:selected');
        const productId = selectedOption.val();

        $.ajax({
            url: "/admin/product/rate/update",
            type: "POST",
            data: {
                _token: csrfToken,
                product_id: productId,
            },
            success: function(response) {
                if(response.rate){
                    $('#rate').val(response.rate);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonColor: '#4154f1'
                    });
                }
            },
            error: function(xhr, error){
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#4154f1'
                });
            }
        });
    });

    // Toggle chambers section visibility
    $('#showChamberBtn').click(function() {
        $('#chambersSection').slideToggle(300);
        $(this).find('i').toggleClass('bi-plus-circle bi-dash-circle');
    });

    // Calculate amount when rate or stock changes
    $('input[name="rate"], input[name="stock"]').on('input', function() {
        const rate = parseFloat($('input[name="rate"]').val()) || 0;
        const stock = parseFloat($('input[name="stock"]').val()) || 0;
        $('input[name="amount"]').val((rate * stock).toFixed(2));
    });

    $('.chamber-dip, .chamber-rec-dip').on('change', function() {
        let row = $(this).closest('tr');
        let dip = parseFloat(row.find('.chamber-dip').val()) || 0;
        let recDip = parseFloat(row.find('.chamber-rec-dip').val()) || 0;
        let gainLossValue = recDip - dip;
        row.find('.chamber-gain-loss').val(gainLossValue.toFixed(2));

        if(dip == '0' || dip == 0) return;

        let chamberCapacity = parseFloat($('.chamber-capacity').val()) || 0;
        let capacityDip = chamberCapacity / dip;
        let litre = capacityDip * gainLossValue;
        row.find('.chamber-ltr').val(litre.toFixed(2));

        let gain_loss_total = 0;
        $('.chamber-gain-loss').each(function() {
            gain_loss_total += parseFloat($(this).val()) || 0;
        });

        let dip_loss_gain_total = 0;
        $('.chamber-ltr').each(function() {
            dip_loss_gain_total += parseFloat($(this).val()) || 0;
        });

        $("#dip_loss_gain").val(dip_loss_gain_total.toFixed(2));
        $("#gain_span").text(gain_loss_total.toFixed(2));
        $("#liters_span").text(dip_loss_gain_total.toFixed(2));
    });

    // Handle temperature change
    $("#temp_degree").change(function (e) {
        e.preventDefault();
        var tem_degree = $(this).val();
        var fahrenheit_temp = $('#fahrenheit_temp').val();
        var diff = parseFloat(tem_degree) - parseFloat(fahrenheit_temp);
        $("#temp_loss_gain").val(diff.toFixed(1));

        var total_capacity = 0;
        $('.chamber-capacity').each(function() {
            total_capacity += parseFloat($(this).val()) || 0;
        });

        var dip_product = $('input[name="fuel_type"]:checked').val();
        if (dip_product === undefined) {
            swal("Alert", "Please select product type", "info");
            return;
        }

        var measurement = 0;
        if (total_capacity === 60000) {
            if (dip_product === 'super') {
                measurement = parseFloat(diff) * 65;
            } else if (dip_product === 'diesel') {
                measurement = parseFloat(diff) * 51;
            }
        } else if (total_capacity === 50000) {
            if (dip_product === 'super') {
                measurement = parseFloat(diff) * 54;
            } else if (dip_product === 'diesel') {
                measurement = parseFloat(diff) * 43;
            }
        } else if (total_capacity === 48000) {
            if (dip_product === 'super') {
                measurement = parseFloat(diff) * 52;
            } else if (dip_product === 'diesel') {
                measurement = parseFloat(diff) * 40.8;
            }
        }

        $("#end_loss").val(measurement.toFixed(2));
        var dipLossGain = parseFloat($("#dip_loss_gain").val());
        var actual_short = Math.abs(parseFloat(measurement)) - Math.abs(parseFloat(dipLossGain));
        $("#short_loss_gain").val(actual_short.toFixed(2));
    });

    // Add border color on hover
    $('.form-control, .form-select').hover(
        function() { $(this).addClass('border-primary'); },
        function() { $(this).removeClass('border-primary'); }
    );

    // Basic form validation before submission
    $('#purchaseForm').on('submit', function(e) {
        e.preventDefault();

        if ($('#vendor').val() === null || $('#product').val() === null) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select Vendor and Product',
                confirmButtonColor: '#4154f1'
            });
            return false;
        }

        if(parseFloat($('#amount').val()) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Amount should be greater than zero',
                confirmButtonColor: '#4154f1'
            });
            return false;
        }

        if ($('#vehicle_chamber').val() === null) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select Vehicle',
                confirmButtonColor: '#4154f1'
            });
            return false;
        }

        if ($('#driver_name').val() === null) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select Driver',
                confirmButtonColor: '#4154f1'
            });
            return false;
        }

        if ($('#terminal_id').val() === null) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select Terminal',
                confirmButtonColor: '#4154f1'
            });
            return false;
        }

        if ($('#tank_update').val() === null) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select Tank',
                confirmButtonColor: '#4154f1'
            });
            return false;
        }
        // var chamberData = [];
        // $('tbody tr').each(function(index) {
        //     if (index < 4) { // Only process the first 4 chambers
        //         var chamber = {
        //             number: $(this).find('input[name^="chamber"][name$="[number]"]').val(),
        //             capacity: $(this).find('input[name^="chamber"][name$="[capacity]"]').val(),
        //             dip: $(this).find('input[name^="chamber"][name$="[dip]"]').val(),
        //             rec_dip: $(this).find('input[name^="chamber"][name$="[rec_dip]"]').val(),
        //             gain_loss: $(this).find('input[name^="chamber"][name$="[gain_loss]"]').val(),
        //             ltr: $(this).find('input[name^="chamber"][name$="[ltr]"]').val()
        //         };
        //         chamberData.push(chamber);
        //     }
        // });

        const form = document.getElementById('purchaseForm');
        var formData = new FormData(form);
        formData.append('vendor_data_name', $('#vendor').find(':selected').data('name').replace('&amp;', '&'));
        formData.append('vendor_data_type', $('#vendor').find(':selected').data('type'));

        // for (let [key, value] of formData.entries()) {
        //     console.log(key, value);
        // }

        var fuelType = $('input[name="fuel_type"]:checked').val();
        if ($('#chambersSection').is(':visible') && !fuelType) {
            Swal.fire({
                icon: 'warning',
                title: 'Product Type Required',
                text: 'Please select a product type (Super or Diesel)',
                confirmButtonColor: '#4154f1'
            });
            return false;
        }

        $('button[type="submit"]').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Processing...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Purchase added successfully',
                        confirmButtonColor: '#4154f1'
                    }).then(function() {
                        window.location.href = response.redirect || '/admin/purchase';
                    });
                } else if(response.error === 'tank-limit-exceed') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tank Capacity Issue',
                        text: 'Not enough tank capacity available',
                        confirmButtonColor: '#4154f1'
                    });
                    $('button[type="submit"]').prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Purchase');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Something went wrong. Please try again.',
                        confirmButtonColor: '#4154f1'
                    });
                    $('button[type="submit"]').prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Purchase');
                }
            },
            error: function(xhr) {
                var errorMessage = 'Something went wrong. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var firstError = Object.values(errors)[0];
                    errorMessage = firstError[0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonColor: '#4154f1'
                });

                $('button[type="submit"]').prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Purchase');
            }
        });
    });
});
