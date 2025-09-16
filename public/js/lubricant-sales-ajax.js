document.addEventListener("DOMContentLoaded", function () {
    if (window.jQuery && $("#lubricantSalesTable").length) {
        $("#lubricantSalesTable").DataTable({
            processing: true,
            responsive: false,
            scrollX: true,
            dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"],
            ],
            pageLength: 25,
            order: [[0, "desc"]],
            columnDefs: [{ targets: "_all", className: "text-center" }],
        });
    }

    // Open modal
    $(document).on("click", "#openLubricantSalesModalBtn", function () {
        resetLubricantForm();
        var modal = new bootstrap.Modal(
            document.getElementById("lubricantSalesModal")
        );
        modal.show();
    });

    function resetLubricantForm() {
        $("#lubricant_nozzles_div").html("");
        $("#lubricant_notes").val("");
        // Build rows for each product (from dataset embedded in DOM)
        var products = window.lubricantProducts || [];
        products.forEach(function (p) {
            var opening = parseFloat(p.opening || 0);
            var rate = parseFloat(p.rate || 0);
            $("#lubricant_nozzles_div").append(`
                <div class="row mb-3 gx-2 align-items-end">
                    <div class="col-12 col-md-3 d-flex align-items-center">
                        <p class="mb-0">${p.name}</p>
                        <input type="hidden" value="${p.id}" id="product_${p.id}">
                    </div>
                    <div class="col-12 col-md-2"><label class="form-label">Opening Stock</label><input disabled class="form-control opening_reading" type="number" value="${opening}" step="0.01" id="opening_reading_${p.id}"></div>
                    <div class="col-12 col-md-2"><label class="form-label">Quantity</label><input class="form-control quantity" type="number" step="1" value="0" id="quantity_${p.id}"></div>
                    <div class="col-12 col-md-2"><label class="form-label">Rate</label><input disabled class="form-control sale_rate" type="number" step="0.01" value="${rate}" id="rate_${p.id}"></div>
                    <div class="col-12 col-md-2"><label class="form-label">Amount (Rs)</label><input class="form-control total_amount" type="number" step="0.01" value="0" id="total_amount_${p.id}" disabled><input type="hidden" value="${p.tank_id}" id="selected_tank_${p.id}"></div>
                </div>
            `);
        });
        totals();
    }

    $(document).on("change", ".sale_rate, .quantity", function () {
        totals();
    });

    function totals() {
        var grandtotalamount = 0;
        var actual_quantity_total = 0;
        $(".opening_reading").each(function () {
            var id = $(this).attr("id");
            var product_id = id.split("_").pop();
            $("#lubricant_add_sales_btn")
                .attr("disabled", false)
                .text("Submit");
            var closing_reading =
                parseFloat($("#opening_reading_" + product_id).val() || 0) +
                parseFloat($("#quantity_" + product_id).val() || 0);
            var opening_reading = parseFloat(
                $("#opening_reading_" + product_id).val() || 0
            );
            var sale_rate = parseFloat($("#rate_" + product_id).val() || 0);
            if (closing_reading < opening_reading) {
                Swal.fire({
                    icon: "info",
                    title: "Alert",
                    text: "Quantity can not be negative",
                    confirmButtonColor: "#4154f1",
                });
                $("#lubricant_add_sales_btn")
                    .attr("disabled", true)
                    .text("Submit");
                return false;
            }
            var quantity = parseFloat($("#quantity_" + product_id).val() || 0);
            var actual_quantity = quantity;
            var total_amount = actual_quantity * sale_rate;
            actual_quantity_total += actual_quantity;
            grandtotalamount += total_amount;
            if (actual_quantity < 0) {
                Swal.fire({
                    icon: "info",
                    title: "Alert",
                    text: "Quantity can not be less than 0",
                    confirmButtonColor: "#4154f1",
                });
                $("#lubricant_add_sales_btn")
                    .attr("disabled", true)
                    .text("Submit");
                return false;
            }
            if (opening_reading < actual_quantity) {
                Swal.fire({
                    icon: "info",
                    title: "Alert",
                    text: "Insufficient stock",
                    confirmButtonColor: "#4154f1",
                });
                $("#lubricant_add_sales_btn")
                    .attr("disabled", true)
                    .text("Submit");
                return false;
            }
            $("#quantity_" + product_id).val(actual_quantity);
            $("#total_amount_" + product_id).val(total_amount.toFixed(2));
        });
        $("#lubricant_quantity_total").html(
            "<b>" + actual_quantity_total.toFixed(2) + "</b>"
        );
        $("#lubricant_amount_total").html(
            "<b>" + grandtotalamount.toFixed(2) + " Rs </b>"
        );
    }

    // $(document).on('submit', '#lubricant_sales_form', function(e){
    //     e.preventDefault();
    //     $('#lubricant_add_sales_btn').attr('disabled', true).text('Please wait...');
    //     var vendor_id = 7, vendor_type = 7, vendor_name = 'cash';
    //     var requests = [];
    //     $(".opening_reading").each(function(){
    //         var id = $(this).attr('id');
    //         var product_id = id.split('_').pop();
    //         if ($('#quantity_'+product_id).val()==='0'){ return true; }
    //         var closing_reading = parseFloat($('#opening_reading_'+product_id).val()||0) + parseFloat($('#quantity_'+product_id).val()||0);
    //         var payload = {
    //             _token: $('meta[name="csrf-token"]').attr('content'),
    //             product_id: product_id,
    //             customer_id: vendor_id,
    //             vendor_type: vendor_type,
    //             vendor_name: vendor_name,
    //             amount: $('#total_amount_'+product_id).val(),
    //             quantity: $('#quantity_'+product_id).val(),
    //             rate: $('#rate_'+product_id).val(),
    //             notes: $('#lubricant_notes').val(),
    //             sale_date: $('#lubricant_sale_date').val(),
    //             selected_tank: $('#selected_tank_'+product_id).val(),
    //             opening_reading: $('#opening_reading_'+product_id).val(),
    //             closing_reading: closing_reading
    //         };
    //         requests.push($.post({ url: window.lubricantStoreUrl || '/sales/lubricant/store', data: payload }));
    //     });

    //     $.when.apply($, requests).done(function(){
    //         Swal.fire({ icon:'success', title:'Success', text:'Sales added successfully', confirmButtonColor:'#4154f1' }).then(()=>{ location.reload(); });
    //     }).fail(function(xhr){
    //         var msg = 'Failed to add sales, please try again';
    //         if (xhr && xhr.responseJSON && xhr.responseJSON.error === 'tank-limit-exceed') { msg = "Tank stock is less than the stock you're selling"; }
    //         Swal.fire({ icon:'error', title:'Error', text: msg, confirmButtonColor:'#4154f1' });
    //         $('#lubricant_add_sales_btn').attr('disabled', false).text('Submit');
    //     });
    // });

    $(document).on("submit", "#lubricant_sales_form", function (e) {
        e.preventDefault();
        $("#lubricant_add_sales_btn")
            .attr("disabled", true)
            .text("Please wait...");

        var vendor_id = 7,
            vendor_type = 7,
            vendor_name = "cash";
        var requests = [];

        $(".opening_reading").each(function () {
            var id = $(this).attr("id");
            var product_id = id.split("_").pop();

            if ($("#quantity_" + product_id).val() === "0") {
                return true; // skip if quantity is zero
            }

            var closing_reading =
                parseFloat($("#opening_reading_" + product_id).val() || 0) +
                parseFloat($("#quantity_" + product_id).val() || 0);

            var payload = {
                _token: $('meta[name="csrf-token"]').attr("content"),
                product_id: product_id,
                customer_id: vendor_id,
                vendor_type: vendor_type,
                vendor_name: vendor_name,
                amount: $("#total_amount_" + product_id).val(),
                quantity: $("#quantity_" + product_id).val(),
                rate: $("#rate_" + product_id).val(),
                notes: $("#lubricant_notes").val(),
                sale_date: $("#lubricant_sale_date").val(),
                selected_tank: $("#selected_tank_" + product_id).val(),
                opening_reading: $("#opening_reading_" + product_id).val(),
                closing_reading: closing_reading,
            };

            requests.push(
                $.ajax({
                    url: window.lubricantStoreUrl || "/sales/lubricant/store",
                    type: "POST",
                    data: payload,
                })
            );
        });

        if (requests.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "No Sales",
                text: "Please enter at least one sale before submitting.",
                confirmButtonColor: "#4154f1",
            });
            $("#lubricant_add_sales_btn")
                .attr("disabled", false)
                .text("Submit");
            return;
        }

        $.when
            .apply($, requests)
            .done(function () {
                var responses = arguments;

                // Normalize single vs multiple requests
                if (requests.length === 1) {
                    responses = [arguments];
                }

                var allSuccess = true;
                var errorMsg = "";

                $.each(responses, function (i, res) {
                    var data = res[0]; // [data, statusText, jqXHR]
                    if (!(data && data.success)) {
                        allSuccess = false;
                        errorMsg =
                            data.message ||
                            "Failed to add some sales, please try again";
                    }
                });

                if (allSuccess) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: "Sales added successfully",
                        confirmButtonColor: "#4154f1",
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: errorMsg,
                        confirmButtonColor: "#4154f1",
                    });
                    $("#lubricant_add_sales_btn")
                        .attr("disabled", false)
                        .text("Submit");
                }
            })
            .fail(function (xhr) {
                var msg = "Failed to add sales, please try again";
                if (
                    xhr &&
                    xhr.responseJSON &&
                    xhr.responseJSON.error === "tank-limit-exceed"
                ) {
                    msg = "Tank stock is less than the stock you're selling";
                }
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: msg,
                    confirmButtonColor: "#4154f1",
                });
                $("#lubricant_add_sales_btn")
                    .attr("disabled", false)
                    .text("Submit");
            });
    });

    $("#printLubricantReportBtn").on("click", function () {
        const w = window.open("", "_blank");
        const tableHTML = $("#lubricantSalesTable")[0].outerHTML;
        const currentDate = new Date().toLocaleDateString();
        w.document.write(
            `<!DOCTYPE html><html><head><title>Lubricant Sales Report</title><style>body{font-family:Arial;margin:20px;font-size:12px}.header{text-align:center;margin-bottom:30px;border-bottom:2px solid #333;padding-bottom:15px}table{width:100%;border-collapse:collapse;margin-bottom:20px}th,td{border:1px solid #ddd;padding:8px;text-align:center}th{background:#f5f5f5}</style></head><body><div class="header"><h1>Lubricant Sales Report</h1><p>Generated on: ${currentDate}</p></div>${tableHTML}</body></html>`
        );
        w.document.close();
        w.print();
    });

    $(document).on("click", ".delete-sales-btn", function (e) {
        e.preventDefault();
        console.log("Delete button clicked");
        var salesId = $(this).data("id");
        console.log("Sales ID:", salesId);
        if (salesId) {
            deleteSale(salesId);
        } else {
            alert("Sales ID not found");
        }
    });

    function deleteSale(saleId) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/sales/lubricant/" + saleId,
                    type: "POST",
                    data: {
                        _method: "DELETE",
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        sale_id: saleId,
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.message,
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Something went wrong. Please try again.",
                        });
                    },
                });
            }
        });
    }

    // Delete sale (only shows for latest sale per product and admin)
    $(document).on("click", ".delete-sale-btn", function () {
        var saleId = $(this).data("id");
        Swal.fire({
            icon: "warning",
            title: "Are you sure?",
            text: "This will delete the selected sale.",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: window.saleDeleteUrl || "/sales/delete",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    sales_id: saleId,
                },
                success: function (resp) {
                    if (resp && resp.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Deleted",
                            text: resp.message || "Sale deleted",
                            confirmButtonColor: "#4154f1",
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text:
                                (resp && resp.message) ||
                                "Failed to delete sale",
                            confirmButtonColor: "#4154f1",
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to delete sale",
                        confirmButtonColor: "#4154f1",
                    });
                },
            });
        });
    });
});
