$(document).ready(function() {
    console.log('Document ready');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log('CSRF Token:', csrfToken);

    // Initialize sales create page functionality
    if (typeof $('#salesForm').length !== 'undefined' && $('#salesForm').length > 0) {
        console.log('Sales form found, initializing create page');
        initializeSalesCreate();
    }

    // Initialize sales index page functionality
    if (typeof $('#salesTable').length !== 'undefined' && $('#salesTable').length > 0) {
        console.log('Sales table found, initializing index page');
        initializeSalesIndex();
    }

    /**
     * Initialize sales create page functionality
     */
    function initializeSalesCreate() {
        // Handle form submission
        $("#salesForm").submit(function(e) {
            e.preventDefault();

            // Validation
            if ($('#supplier_id').val() === null || $('#product_id').val() === null) {
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

            if(parseFloat($('#quantity').val()) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Quantity',
                    text: 'Quantity should be greater than zero',
                    confirmButtonColor: '#4154f1'
                });
                return false;
            }

            if ($('#selected_tank').val() === null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select Tank',
                    confirmButtonColor: '#4154f1'
                });
                return false;
            }

            $("#add_sales_btn").attr("disabled", true);
            $("#add_sales_btn").html(
                `Please wait...`
            );

            var profit_loss_status = 0;
            if ($("#profitLossCheckBoss").prop("checked")) {
                profit_loss_status = 1;
            }

            var vendordropdown = $("#supplier_id").find("option:selected");
            var vendor_id = $("#supplier_id").val();
            var vendor_type = vendordropdown.data("type");
            var vendor_name = vendordropdown.data("name");

            // Create form data
            var formData = new FormData();
            formData.append("_token", csrfToken);
            formData.append("product_id", $("#product_id").val());
            formData.append("customer_id", vendor_id);
            formData.append("vendor_type", vendor_type);
            formData.append("vendor_name", vendor_name);
            formData.append("terminal_id", $("#terminal_id").val() || 0);
            formData.append("tank_lari_id", $("#tank_lari_id").val() || 0);
            formData.append("amount", $("#amount").val() || 0);
            formData.append("quantity", $("#quantity").val() || 0);
            formData.append("rate", $("#rate").val() || 0);
            formData.append("notes", $("#notes").val() || "");
            formData.append("freight", $("#freight option:selected").val() || 0);
            formData.append("freight_charges", $("#freight_charges").val() || 0);
            formData.append("sales_type", $("#sales_type").val() || 1);
            formData.append("sale_date", $("#sale_date").val() || "");
            formData.append("profit_loss_status", profit_loss_status);
            formData.append("selected_tank", $("#selected_tank").val() || 0);

            $.ajax({
                url: "/admin/sales",
                type: "POST",
                processData: false,
                contentType: false,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Sales added successfully',
                            confirmButtonColor: '#4154f1'
                        }).then(function() {
                            window.location.href = response.redirect || '/admin/sales';
                        });
                    } else if(response.error === 'tank-limit-exceed') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tank Stock Issue',
                            text: 'Tank stock is less than the stock you\'re selling',
                            confirmButtonColor: '#4154f1'
                        });
                    } else if(response.error === 'outofstock') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock Issue',
                            text: 'Product stock is less than you\'re selling!',
                            confirmButtonColor: '#4154f1'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to add sales, please try again',
                            confirmButtonColor: '#4154f1'
                        });
                    }
                    $("#add_sales_btn").attr("disabled", false);
                    $("#add_sales_btn").html(`Submit`);
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
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonColor: '#4154f1'
                    });

                    $("#add_sales_btn").attr("disabled", false);
                    $("#add_sales_btn").html(`Submit`);
                }
            });
        });

        // Handle product change for tank loading
        $("#product_id").change(function(){
            var selectedOption = $("#product_id option:selected");
            var product_id = selectedOption.val();

            $.ajax({
                url: "/admin/product/tank/update",
                type: "POST",
                data: {
                    _token: csrfToken,
                    product_id: product_id,
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if(response.success && response.tanks){
                        $("#selected_tank").html('');
                        $("#selected_tank").append(`<option value="" disabled selected>Tanks</option>`);
                        $.each(response.tanks, function(key, value){
                            $("#selected_tank").append(`<option value="${value.id}">${value.tank_name}</option>`);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load tanks',
                            confirmButtonColor: '#4154f1'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load tanks. Please try again.',
                        confirmButtonColor: '#4154f1'
                    });
                }
            });
        });

        // Calculate amount when quantity or rate changes
        let amount = 0;
        let quantity = 0;
        let rate = 0;

        $("#quantity").change(function(e){
            e.preventDefault();
            quantity = $("#quantity").val();
            amount = quantity * rate;
            $("#amount").val(amount.toFixed(2));
        });

        $("#rate").change(function(e){
            e.preventDefault();
            rate = $("#rate").val();
            amount = quantity * rate;
            $("#amount").val(amount.toFixed(2));
        });

        // Initialize form values
        $("#product_id").val("");
        $("#supplier_id").val("");
        $("#tank_lari_id").val("");
    }

    /**
     * Initialize sales index page functionality
     */
    function initializeSalesIndex() {
        console.log('Initializing sales index page');
        $('#salesTable').DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            pageLength: 10,
            order: [[0, 'asc']],
        });

        $('#addNewSalesBtn').click(function() {
            window.location.href = "/admin/sales/create";
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Handle print report button
        $('#printReportBtn').on('click', function() {
            printSalesReport();
        });

        // Handle delete sales button with event delegation
        $(document).on('click', '.delete-sales-btn', function(e) {
            e.preventDefault();
            console.log('Delete button clicked');
            var salesId = $(this).data('id');
            console.log('Sales ID:', salesId);
            if (salesId) {
                deleteSale(salesId);
            } else {
                alert('Sales ID not found');
            }
        });
    }

    /**
     * Delete sale function
     */
    function deleteSale(sale_id) {
        console.log('Delete sale function called with ID:', sale_id);
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/admin/sales/delete",
                    type: "POST",
                    data: {
                        _token: csrfToken,
                        sales_id: sale_id,
                    },
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message || 'Sales deleted successfully!',
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to delete sales, please try again!'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete sales, please try again!'
                        });
                    }
                });
            }
        });
    }

    /**
     * Print sales report
     */
    function printSalesReport() {
        const printWindow = window.open('', '_blank');
        const printContent = generateSalesPrintContent();

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }

    /**
     * Generate sales print content
     */
    function generateSalesPrintContent() {
        const currentDate = new Date().toLocaleDateString();
        const tableHTML = $('#salesTable')[0].outerHTML;

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Sales Report</title>
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
                    <h1>Sales Report</h1>
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

    // Add border color on hover
    $('.form-control, .form-select').hover(
        function() { $(this).addClass('border-primary'); },
        function() { $(this).removeClass('border-primary'); }
    );

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.which === 80) {
            e.preventDefault();
            if (typeof printSalesReport === 'function') {
                printSalesReport();
            }
        }
    });

    /**
     * Print div function for sales cards
     */
    window.printDiv = function(divId) {
        var printContents = document.getElementById(divId).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    };
});
