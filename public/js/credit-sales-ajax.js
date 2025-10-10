$(document).ready(function() {
    // Initialize DataTable
    $('#creditSalesTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "pageLength": 25,
        "order": [[0, "desc"]]
    });

    // Customer change event - load customer vehicles
    $("#customer_id").change(function(){
        var selectedOption = $("#customer_id option:selected");
        var customer_id = selectedOption.val();

        if (customer_id) {
            $.ajax({
                url: '/sales/credit/customer-vehicles',
                type: "POST",
                data: {
                    "customer_id": customer_id,
                    "_token": $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.success) {
                        // Destroy existing select2 instance if it exists
                        if ($("#tank_lari_id").hasClass('select2-hidden-accessible')) {
                            $("#tank_lari_id").select2('destroy');
                        }

                        // Clear and populate dropdown
                        $("#tank_lari_id").html('<option value="" selected disabled>Choose vehicle</option>');
                        $.each(data.vehicles, function (key, value) {
                            $("#tank_lari_id").append(`<option value="${value.id}">${value.larry_name}</option>`);
                        });

                        // Re-initialize select2 for searchable functionality
                        $("#tank_lari_id").select2({
                            placeholder: "Type to search...",
                            allowClear: false,
                        });
                    } else {
                        showAlert('Error', data.message || 'Failed to load vehicles', 'error');
                    }
                },
                error: function() {
                    showAlert('Error', 'Failed to load customer vehicles', 'error');
                }
            });
        } else {
            // Reset vehicle dropdown when no customer is selected
            if ($("#tank_lari_id").hasClass('select2-hidden-accessible')) {
                $("#tank_lari_id").select2('destroy');
            }
            $("#tank_lari_id").html('<option value="" selected disabled>Choose vehicle</option>');
            $("#tank_lari_id").select2({
                placeholder: "Type to search...",
                allowClear: false,
            });
        }
    });

    // Product change event - load tanks and rate
    $("#product_id").change(function(){
        var selectedOption = $("#product_id option:selected");
        var product_id = selectedOption.val();

        if (product_id) {
            // Load tanks for the product
            $.ajax({
                url: '/sales/credit/tanks',
                type: "POST",
                data: {
                    "product_id": product_id,
                    "_token": $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.success) {
                        $("#selected_tank").html('');
                        $.each(data.tanks, function (key, value) {
                            $("#selected_tank").append(`<option value="${value.id}">${value.tank_name}</option>`);
                        });
                    } else {
                        showAlert('Error', data.message || 'Failed to load tanks', 'error');
                    }
                },
                error: function() {
                    showAlert('Error', 'Failed to load tanks', 'error');
                }
            });

            // Load product rate
            $.ajax({
                url: '/sales/credit/product-rate',
                type: "POST",
                data: {
                    "product_id": product_id,
                    "_token": $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.success) {
                        $("#rate").val(data.current_sale);
                        calculateAmount();
                    } else {
                        showAlert('Error', data.message || 'Failed to load product rate', 'error');
                    }
                },
                error: function() {
                    showAlert('Error', 'Failed to load product rate', 'error');
                }
            });
        }
    });

    // Calculate amount when quantity or rate changes
    let amount = 0;
    let quantity = 0;
    let rate = 0;

    $("#quantity").on('input change', function (e) {
        calculateAmount();
    });

    $("#rate").on('input change', function (e) {
        calculateAmount();
    });

    function calculateAmount() {
        quantity = parseFloat($("#quantity").val()) || 0;
        rate = parseFloat($("#rate").val()) || 0;
        amount = quantity * rate;
        $("#amount").val(amount.toFixed(2));
    }

    // Form validation helper
    function isNumber(value) {
        return !isNaN(parseFloat(value)) && isFinite(value) && parseFloat(value) > 0;
    }

    // Credit sales form submission
    $("#credit_sales_form").submit(function (e) {
        e.preventDefault();

        // Disable submit button
        $("#transaction_btn").attr("disabled", true);
        $("#transaction_btn").html(`Please wait... <i class="fa fa-spinner fa-spin"></i>`);

        // Get form data
        var vendorDropdown = $("#customer_id").find('option:selected');
        var vendor_id = $("#customer_id").val();
        var vendor_type = vendorDropdown.data('type');
        var vendor_name = vendorDropdown.data('name');
        var transaction_amount = $("#amount").val();
        var transaction_date = $("#transaction_date").val();
        var transaction_description = $("#transaction_description").val();
        var invoice_no = $("#invoice_no").val();
        var product_id = $("#product_id").val();
        var tank_id = $("#selected_tank").val();
        var vehicle_id = $("#tank_lari_id").val();
        var quantity = $("#quantity").val();
        var rate = $("#rate").val();

        // Validate amount
        var isValidNumber = isNumber(transaction_amount);
        if (!isValidNumber) {
            showAlert("Alert", "Invalid Amount", "info");
            resetSubmitButton();
            return;
        }

        // Validate required fields
        if (!vendor_id || !product_id || !tank_id || !vehicle_id || !quantity || !rate || !transaction_description || !invoice_no) {
            showAlert("Alert", "Please fill all required fields", "info");
            resetSubmitButton();
            return;
        }

        // Prepare AJAX data
        var formData = new FormData();
        formData.append("vendor_id", vendor_id);
        formData.append("vendor_type", vendor_type);
        formData.append("vendor_name", vendor_name);
        formData.append("transaction_amount", transaction_amount);
        formData.append("transaction_date", transaction_date);
        formData.append("transaction_description", transaction_description);
        formData.append("invoice_no", invoice_no);
        formData.append("product_id", product_id);
        formData.append("tank_id", tank_id);
        formData.append("vehicle_id", vehicle_id);
        formData.append("quantity", quantity);
        formData.append("rate", rate);
        formData.append("customer_id", vendor_id);
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));

        // Submit form
        $.ajax({
            url: '/sales/credit/store',
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: data.success ? 'Success' : 'Error',
                        text: data.message || 'Credit Sale created successfully',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: data.success ? 'Success' : 'Error',
                        text: data.message || 'Please try again',
                        confirmButtonText: 'OK'
                    });
                }
                resetSubmitButton();
            },
            error: function(xhr, status, error) {
                var errorMessage = "Please try again";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: data.success ? 'Success' : 'Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
                resetSubmitButton();
            }
        });
    });

    // Reset submit button
    function resetSubmitButton() {
        $("#transaction_btn").attr("disabled", false);
        $("#transaction_btn").html(`<i class="bi bi-check-circle me-2"></i>Submit`);
    }

    // Delete credit sale
    $(document).on('click', '.delete-credit-sale-btn', function() {
        var tid = $(this).data("id");
        var ledgerpurchasetype = $(this).data("ledgerpurchasetype");

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to delete this credit sale?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/sales/credit/delete',
                    type: "POST",
                    data: {
                        tid: tid,
                        ledgerpurchasetype: ledgerpurchasetype,
                        "_token": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Credit sale has been deleted successfully.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Please try again!'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete credit sale!'
                        });
                    }
                });
            }
        });
    });

    // Print report functionality (bottom button) - Using event delegation
    $(document).on('click', '#printCreditSalesReportBtn', function() {
        console.log('Bottom print button clicked'); // Debug log
        printCreditSalesReport();
    });

    // Print table functionality (top button) - Using event delegation
    $(document).on('click', '#printCreditSalesTableBtn', function() {
        console.log('Top print button clicked'); // Debug log
        printCreditSalesTable();
    });

    // Helper function for alerts
    function showAlert(title, text, icon) {
        return Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonText: 'OK'
        });
    }

    // Reset form values on page load
    $("#product_id").val("");
    $("#customer_id").val("");
    $("#tank_lari_id").val("");
    $("#selected_tank").val("");
    $("#quantity").val("");
    $("#rate").val("");
    $("#amount").val("");
    $("#transaction_description").val("");
    $("#invoice_no").val("");

    // Set document title
    document.title = "Credit Sales";
});

/**
 * Print Credit Sales Table Only - Enhanced version similar to ledger
 */
window.printCreditSalesTable = function() {
    console.log('printCreditSalesTable called'); // Debug log
    const printWindow = window.open('', '_blank');
    const printContent = generateCreditSalesTablePrintContent();

    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
};

/**
 * Print Credit Sales Summary Report - Enhanced version similar to ledger
 */
window.printCreditSalesReport = function() {
    console.log('printCreditSalesReport called'); // Debug log
    const printWindow = window.open('', '_blank');
    const printContent = generateCreditSalesReportPrintContent();

    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
};

/**
 * Generate comprehensive print content for Credit Sales Table only
 */
window.generateCreditSalesTablePrintContent = function() {
    const currentDate = new Date().toLocaleDateString();
    const tableHTML = $('#creditSalesTable')[0].outerHTML;

    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Credit Sales Records Report</title>
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
                .fw-medium {
                    font-weight: 500;
                }
                .table-light {
                    background-color: #f8f9fa;
                }
                .table-info {
                    background-color: #d1ecf1;
                }
                .badge {
                    font-size: 10px;
                    padding: 2px 6px;
                    border-radius: 3px;
                    font-weight: bold;
                }
                .bg-secondary {
                    background-color: #6c757d !important;
                    color: white;
                }
                .bg-success {
                    background-color: #28a745 !important;
                    color: white;
                }
                .bg-danger {
                    background-color: #dc3545 !important;
                    color: white;
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
                <h1>Credit Sales Records Report</h1>
                <p>Generated on: ${currentDate}</p>
            </div>
            ${tableHTML}
            <div class="footer">
                <p>This report was generated automatically by the Stock Management System</p>
            </div>
        </body>
        </html>
    `;
};

/**
 * Generate comprehensive print content for Credit Sales Summary Report
 */
window.generateCreditSalesReportPrintContent = function() {
    const currentDate = new Date().toLocaleDateString();
    const summaryHTML = $('#sales_summary_card')[0].outerHTML;

    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Credit Sales Summary Report</title>
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
                .fw-medium {
                    font-weight: 500;
                }
                .table-light {
                    background-color: #f8f9fa;
                }
                .table-info {
                    background-color: #d1ecf1;
                }
                .table-primary {
                    background-color: #d1ecf1;
                }
                .badge {
                    font-size: 10px;
                    padding: 2px 6px;
                    border-radius: 3px;
                    font-weight: bold;
                }
                .bg-secondary {
                    background-color: #6c757d !important;
                    color: white;
                }
                .bg-success {
                    background-color: #28a745 !important;
                    color: white;
                }
                .bg-danger {
                    background-color: #dc3545 !important;
                    color: white;
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
                <h1>Credit Sales Summary Report</h1>
                <p>Generated on: ${currentDate}</p>
            </div>

            ${summaryHTML}

            <div class="footer">
                <p>This report was generated automatically by the Stock Management System</p>
            </div>
        </body>
        </html>
    `;
};
