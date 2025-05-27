$(document).ready(function () {
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $("#addSalesForm").submit(function (e) {
        e.preventDefault();

                e.preventDefault();

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
            `Please wait...<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>`
        );

        // var sale_type = 0;
        // var is_checked = $("#cash_type_input").is(":checked");
        // if (is_checked) {
        //     sale_type = 1;
        // } else {
        //     sale_type = 0;
        // }

        var profit_loss_status = 0;
        if ($("#profitLossCheckBoss").prop("checked")) {
            profit_loss_status = 1;
        }

        var vendordropdown = $("#supplier_id").find("option:selected");
        var vendor_id = $("#supplier_id").val();
        var vendor_type = vendordropdown.data("type");
        var vendor_name = vendordropdown.data("name");

        //append into ajax data
        var ajax_data = new FormData();
        ajax_data.append("product_id", $("#product_id").val());
        ajax_data.append("customer_id", vendor_id);
        ajax_data.append("vendor_type", vendor_type);
        ajax_data.append("vendor_name", vendor_name);
        ajax_data.append("terminal_id", $("#terminal_id").val());
        ajax_data.append("tank_lari_id", $("#tank_lari_id").val());
        ajax_data.append("amount", $("#amount").val());
        ajax_data.append("quantity", $("#quantity").val());
        ajax_data.append("rate", $("#rate").val());
        ajax_data.append("notes", $("#notes").val());
        ajax_data.append("freight", $("#freight option:selected").val());
        ajax_data.append("freight_charges", $("#freight_charges").val());
        ajax_data.append("sales_type", $("#sales_type").val());
        ajax_data.append("sale_date", $("#sale_date").val());
        ajax_data.append("profit_loss_status", profit_loss_status);
        ajax_data.append("sale_type", sale_type);
        ajax_data.append("selected_tank", $("#selected_tank").val());
        $.ajax({
            url: "serverside/post.php",
            type: "POST",
            processData: false,
            contentType: false,
            data: ajax_data,
            success: function (data) {
                if (data.trim() == "true") {
                    swal(
                        "Success",
                        "Sales added successfully ",
                        "success"
                    ).then((value) => {
                        location.reload();
                    });
                } else if (data.trim() == "outofstock") {
                    swal(
                        "Alert",
                        "Product stock is less than you're selling!",
                        "info"
                    );
                } else if (data.trim() == "tank-limit-exceed") {
                    swal(
                        "Alert",
                        "Tank stock is less than the stock you're selling",
                        "info"
                    );
                } else {
                    swal(
                        "Error",
                        "Failed to add sales, please try again ",
                        "error"
                    );
                }
                $("#add_sales_btn").attr("disabled", false);
                $("#add_sales_btn").html(`Submit`);
            },
        });
    });
});
