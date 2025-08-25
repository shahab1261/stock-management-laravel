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

    // Delete purchase function (matching old PHP project)
    function deletePurchase(purchase_id, tank_id, purchasedstock) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    url: "/admin/purchases/delete",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        purchase_id: purchase_id,
                        tank_id: tank_id,
                        purchasedstock: purchasedstock,
                    },
                    success: function (data) {
                        if (data.trim() == "true") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Purchase deleted successfully!',
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to delete purchase, please try again!'
                            });
                        }
                    }//success
                });//ajax
            }
        });
    }//Delete purchase

    /**
     * Print purchase report
     */
    function printPurchaseReport() {
        const printWindow = window.open('', '_blank');
        const printContent = generatePurchasePrintContent();

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }

    /**
     * Generate purchase print content
     */
    function generatePurchasePrintContent() {
        const currentDate = new Date().toLocaleDateString();
        const tableHTML = $('#purchaseTable')[0].outerHTML;

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Purchase Report</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                        font-size: 12px;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 30px;
                        border-bottom: 2px solid #333;
                        padding-bottom: 15px;
                    }
                    .header h1 {
                        margin: 0;
                        color: #333;
                        font-size: 24px;
                    }
                    .header p {
                        margin: 5px 0;
                        color: #666;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f5f5f5;
                        font-weight: bold;
                        text-align: center;
                    }
                    .text-end {
                        text-align: right;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .fw-bold {
                        font-weight: bold;
                    }
                    .table-light {
                        background-color: #f8f9fa;
                    }
                    .table-primary {
                        background-color: #e3f2fd;
                    }
                    .text-success {
                        color: #28a745;
                    }
                    .text-danger {
                        color: #dc3545;
                    }
                    .text-warning {
                        color: #ffc107;
                    }
                    .footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 10px;
                        color: #666;
                        border-top: 1px solid #ddd;
                        padding-top: 10px;
                    }
                    @media print {
                        body { margin: 0; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Purchase Report</h1>
                    <p>Generated on: ${currentDate}</p>
                </div>
                ${tableHTML}
                <div class="footer">
                    <p>This report was generated automatically by the Stock Management System</p>
                </div>
            </body>
            </html>
        `;
    }

    /**
     * Print chambers report
     */
    function printChambersReport() {
        const printWindow = window.open('', '_blank');
        const printContent = generateChambersPrintContent();

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }

    /**
     * Generate chambers print content
     */
    function generateChambersPrintContent() {
        const currentDate = new Date().toLocaleDateString();
        const tableHTML = $('#chamber_table_body').closest('table')[0].outerHTML;
        const measurementsHTML = $('#measurements_div').html();

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Chambers Information Report</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                        font-size: 12px;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 30px;
                        border-bottom: 2px solid #333;
                        padding-bottom: 15px;
                    }
                    .header h1 {
                        margin: 0;
                        color: #333;
                        font-size: 24px;
                    }
                    .header p {
                        margin: 5px 0;
                        color: #666;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f5f5f5;
                        font-weight: bold;
                        text-align: center;
                    }
                    .text-end {
                        text-align: right;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .fw-bold {
                        font-weight: bold;
                    }
                    .measurements {
                        background-color: #f8f9fa;
                        padding: 15px;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        margin-bottom: 20px;
                    }
                    .footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 10px;
                        color: #666;
                        border-top: 1px solid #ddd;
                        padding-top: 10px;
                    }
                    @media print {
                        body { margin: 0; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Chambers Information Report</h1>
                    <p>Generated on: ${currentDate}</p>
                </div>
                ${tableHTML}
                <div class="measurements">
                    <h3>Measurements Information</h3>
                    ${measurementsHTML}
                </div>
                <div class="footer">
                    <p>This report was generated automatically by the Stock Management System</p>
                </div>
            </body>
            </html>
        `;
    }

    // Initialize purchase index page functionality
    if (typeof $('#purchaseTable').length !== 'undefined' && $('#purchaseTable').length > 0) {
        $('#purchaseTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 10,
            order: [[0, 'asc']],
        });

        $('#addNewPurchaseBtn').click(function() {
            window.location.href = "/admin/purchase/create";
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        $('.show-chambers-btn').on('click', function() {
            var purchaseId = $(this).data('id');

            $('#chamber_table_body').html('');
            $('#measurements_div').html('');
            $('#message_div').addClass('spinner-border text-primary');

            $('#chambersModal').modal('show');

            $.ajax({
                url: "/admin/purchase/chamber/data",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: purchaseId
                },
                success: function(response) {
                    $('#message_div').html('');
                    $('#message_div').removeClass('spinner-border text-primary');

                    if(response.success && response.product_list.length > 0) {
                        // Populate chambers table
                        $.each(response.product_list, function(key, value) {
                            $('#chamber_table_body').append(`
                                <tr>
                                    <td>${key+1}</td>
                                    <td>${value.capacity}</td>
                                    <td>${value.dip_value}</td>
                                    <td>${value.rec_dip_value}</td>
                                    <td>${value.gain_loss}</td>
                                    <td>${value.dip_liters}</td>
                                </tr>
                            `);
                        });

                        // Process measurements
                        var measurements = response.product_list[0].measurements.split('_');

                        $('#measurements_div').html(`
                            <div class="fw-bold text-primary mb-2">Product Information</div>
                            <p><strong>Product:</strong> ${measurements[0]}</p>
                            <p><strong>Invoice.Temp:</strong> ${measurements[1]}</p>
                            <p><strong>Rec.Temp:</strong> ${measurements[2]}</p>
                            <p><strong>Temp Loss/Gain:</strong> ${measurements[3]}</p>
                            <p><strong>Dip Loss/Gain Ltr:</strong> ${measurements[4]}</p>
                            <p><strong>Loss/Gain by temperature:</strong> ${measurements[5]}</p>
                            <p><strong>Actual Short Loss/Gain:</strong> ${measurements[6]}</p>
                        `);
                    } else {
                        $('#message_div').html('<div class="alert alert-primary text-center">No chamber data found for this purchase.</div>');
                    }
                },
                error: function() {
                    $('#message_div').removeClass('spinner-border text-primary');
                    $('#message_div').html('<div class="alert alert-danger text-center">Failed to load chamber data. Please try again.</div>');
                }
            });
        });

        // Handle print chambers button
        $('#printChambersBtn').on('click', function() {
            printChambersReport();
        });

        // Handle print report button
        $('#printReportBtn').on('click', function() {
            printPurchaseReport();
        });

        // Handle delete purchase button
        $('.delete-purchase-btn').on('click', function() {
            var purchaseId = $(this).data('id');
            var tank = $(this).data('tank');
            var stock = $(this).data('stock');

            deletePurchase(purchaseId, tank, stock);
        });
    }
});
