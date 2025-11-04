document.addEventListener("DOMContentLoaded", function () {
    // Centered table and DataTables init matching other pages with "All" option
    if (window.jQuery && $("#salesTable").length) {
        $("#salesTable").DataTable({
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
    $(document).on("click", "#openNozzleSalesModalBtn", function () {
        // Reset fields
        $("#product_id").val("");
        $("#current_rate").html("&nbsp;");
        $("#sale_rate").val("");
        $("#nozzles_div").html("");
        $("#notes").val("");
        var modal = new bootstrap.Modal(
            document.getElementById("nozzleSalesModal")
        );
        modal.show();
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
                    url: "/sales/" + saleId,
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

    // calculate totals on changes
    $(document).on(
        "change",
        ".closing_reading, .sale_rate, .textbox",
        function () {
            getquantityandamount();
        }
    );

    function getquantityandamount() {
        var grandtotalamount = 0;
        var actual_quantity_total = 0;
        $(".closing_reading").each(function () {
            var id = $(this).attr("id");
            var nozzle_id = id.split("_").pop();
            $("#add_sales_btn").attr("disabled", false);
            var closing_reading = parseFloat(
                $("#closing_reading_" + nozzle_id).val() || 0
            );
            var opening_reading = parseFloat(
                $("#opening_reading_" + nozzle_id).val() || 0
            );
            var sale_rate = parseFloat($("#sale_rate").val() || 0);
            var textbox = parseFloat($("#testbox_" + nozzle_id).val() || 0);

            if (closing_reading < opening_reading) {
                $("#add_sales_btn").attr("disabled", true);
                return;
            }
            var quantity = closing_reading - opening_reading;
            var actual_quantity = quantity - textbox;
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
                $("#add_sales_btn").attr("disabled", true);
                return;
            }
            $("#quantity_" + nozzle_id).val(actual_quantity.toFixed(2));
            $("#total_amount_" + nozzle_id).val(total_amount.toFixed(2));
        });
        $("#quantity_total").html(
            "<b>" + actual_quantity_total.toFixed(2) + "</b>"
        );
        $("#amount_total").html(
            "<b>" + grandtotalamount.toFixed(2) + " Rs </b>"
        );
    }

    // product change => load nozzles and show rate
    $(document).on("change", "#product_id", function () {
        var product_id = $(this).val();
        $.post({
            url:
                window.nozzleProductNozzlesUrl ||
                "/sales/nozzle/product-nozzles",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                product_id: product_id,
            },
            success: function (resp) {
                if (!resp.success) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: resp.message || "Failed to load nozzles",
                        confirmButtonColor: "#4154f1",
                    });
                    return;
                }
                $("#current_rate").html(
                    "Rate: <b>Rs " + resp.current_sale + "</b>"
                );
                $("#sale_rate").val(resp.current_sale);
                var nozzles = resp.nozzles || [];
                $("#nozzles_div").html("");
                nozzles.forEach(function (nozzle) {
                    $("#nozzles_div").append(`
                        <div class="col-12 col-md-2 d-flex align-items-center"><p class="mb-0 pt-3">${nozzle.name}</p></div>
                        <div class="col-12 col-md-2"><label class="form-label">Closing Reading</label><input class="form-control closing_reading" value="0" type="number" step="0.01" id="closing_reading_${nozzle.id}" required></div>
                        <div class="col-12 col-md-2"><label class="form-label">Opening Reading</label><input disabled class="form-control" type="number" value="${nozzle.opening_reading}" step="0.01" id="opening_reading_${nozzle.id}" required></div>
                        <div class="col-12 col-md-1"><label class="form-label">Test sales(ltr)</label><input class="form-control textbox" type="number" step="0.01" id="testbox_${nozzle.id}" value="0" required></div>
                        <div class="col-12 col-md-2"><label class="form-label">Quantity (ltr)</label><input class="form-control quantity" type="number" step="0.01" value="0" id="quantity_${nozzle.id}" disabled required></div>
                        <div class="col-12 col-md-2"><label class="form-label">Amount (Rs)</label><input class="form-control total_amount" type="number" step="0.01" value="0" id="total_amount_${nozzle.id}" disabled required><input type="hidden" value="${nozzle.tank_id}" id="selected_tank_${nozzle.id}"><input type="hidden" value="${nozzle.id}" id="nozzle_id_${nozzle.id}"></div>
                    `);
                });
                $("#nozzles_div").append(`
                    <div class="col-12 col-md-2 d-flex align-items-center"></div>
                    <div class="col-12 col-md-2"></div>
                    <div class="col-12 col-md-2"></div>
                    <div class="col-12 col-md-1"></div>
                    <div class="col-12 col-md-2 d-flex align-items-center"><p class="mb-0 pt-3" id="quantity_total"></p></div>
                    <div class="col-12 col-md-2 d-flex align-items-center"><p class="mb-0 pt-3" id="amount_total"></p></div>
                `);
            },
        });
    });

    // submit: precheck then post per nozzle
    // $(document).on("submit", "#addSalesForm_pump", function (e) {
    //     e.preventDefault();
    //     $("#add_sales_btn").attr("disabled", true).text("Please wait...");

    //     var vendor_id = $("#customer_id").val();
    //     var vendor_type = $("#customer_id option:selected").data("type");
    //     var vendor_name = $("#customer_id option:selected").data("name");

    //     $.post({
    //         url: window.nozzlePrecheckUrl || "/sales/nozzle/precheck",
    //         data: {
    //             _token: $('meta[name="csrf-token"]').attr("content"),
    //             product_id: $("#product_id").val(),
    //             sales_date: $("#sale_date").val(),
    //         },
    //         success: function (pre) {
    //             if (!(pre && pre.success)) {
    //                 Swal.fire({
    //                     icon: "error",
    //                     title: "Error",
    //                     text: "Last day dip not found or Sale already recorded for this product and date",
    //                     confirmButtonColor: "#4154f1",
    //                 });
    //                 $("#add_sales_btn").attr("disabled", false).text("Submit");
    //                 return;
    //             }

    //             var requests = [];
    //             $(".closing_reading").each(function () {
    //                 var nozzle_id = $(this).attr("id").split("_").pop();
    //                 if (
    //                     $("#quantity_" + nozzle_id).val() == "0" &&
    //                     $("#testbox_" + nozzle_id).val() == "0"
    //                 ) {
    //                     return true; // continue
    //                 }
    //                 var payload = {
    //                     _token: $('meta[name="csrf-token"]').attr("content"),
    //                     product_id: $("#product_id").val(),
    //                     customer_id: vendor_id,
    //                     vendor_type: vendor_type,
    //                     vendor_name: vendor_name,
    //                     amount: $("#total_amount_" + nozzle_id).val(),
    //                     quantity: $("#quantity_" + nozzle_id).val(),
    //                     rate: $("#sale_rate").val(),
    //                     notes: $("#notes").val(),
    //                     sale_date: $("#sale_date").val(),
    //                     selected_tank: $("#selected_tank_" + nozzle_id).val(),
    //                     nozzle_id: $("#nozzle_id_" + nozzle_id).val(),
    //                     opening_reading: $(
    //                         "#opening_reading_" + nozzle_id
    //                     ).val(),
    //                     closing_reading: $(
    //                         "#closing_reading_" + nozzle_id
    //                     ).val(),
    //                     test_sales: $("#testbox_" + nozzle_id).val(),
    //                 };
    //                 requests.push(
    //                     $.post({
    //                         url: window.nozzleStoreUrl || "/sales/nozzle/store",
    //                         data: payload,
    //                     })
    //                 );
    //             });

    //             $.when
    //                 .apply($, requests)
    //                 .done(function (response) {
    //                     if (response && response.success) {
    //                         Swal.fire({
    //                             icon: "success",
    //                             title: "Success",
    //                             text: response.message || "Sales added successfully",
    //                             confirmButtonColor: "#4154f1",
    //                         }).then(() => {
    //                             location.reload();
    //                         });
    //                     } else {
    //                         Swal.fire({
    //                             icon: "error",
    //                             title: "Error",
    //                             text: response.message || "Failed to add sales, please try again",
    //                             confirmButtonColor: "#4154f1",
    //                         });
    //                     }
    //                 })

    //                 .fail(function (xhr) {
    //                     var msg = "Failed to add sales, please try again";
    //                     if (
    //                         xhr &&
    //                         xhr.responseJSON &&
    //                         xhr.responseJSON.error === "tank-limit-exceed"
    //                     ) {
    //                         msg =
    //                             "Tank stock is less than the stock you're selling";
    //                     }
    //                     Swal.fire({
    //                         icon: "error",
    //                         title: "Error",
    //                         text: msg,
    //                         confirmButtonColor: "#4154f1",
    //                     });
    //                     $("#add_sales_btn")
    //                         .attr("disabled", false)
    //                         .text("Submit");
    //                 });
    //         },
    //         error: function () {
    //             Swal.fire({
    //                 icon: "error",
    //                 title: "Error",
    //                 text: "Precheck failed",
    //                 confirmButtonColor: "#4154f1",
    //             });
    //             $("#add_sales_btn").attr("disabled", false).text("Submit");
    //         },
    //     });
    // });

    $(document).on("submit", "#addSalesForm_pump", function (e) {
        e.preventDefault();
        $("#add_sales_btn").attr("disabled", true).text("Please wait...");

        var vendor_id = $("#customer_id").val();
        var vendor_type = $("#customer_id option:selected").data("type");
        var vendor_name = $("#customer_id option:selected").data("name");

        // Step 1: Precheck before saving
        $.ajax({
            url: window.nozzlePrecheckUrl || "/sales/nozzle/precheck",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                product_id: $("#product_id").val(),
                sales_date: $("#sale_date").val(),
            },
            success: async function (pre) {
                if (!(pre && pre.success)) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Last day dip not found or Sale already recorded for this product and date",
                        confirmButtonColor: "#4154f1",
                    });
                    $("#add_sales_btn").attr("disabled", false).text("Submit");
                    return;
                }

                // Step 2: Collect all nozzles that need requests
                var nozzlesToProcess = [];
                $(".closing_reading").each(function () {
                    var nozzle_id = $(this).attr("id").split("_").pop();
                    var quantity = parseFloat($("#quantity_" + nozzle_id).val() || 0);
                    var testSales = parseFloat($("#testbox_" + nozzle_id).val() || 0);

                    if (quantity != 0 || testSales != 0) {
                        nozzlesToProcess.push({
                            nozzle_id: nozzle_id,
                            payload: {
                                _token: $('meta[name="csrf-token"]').attr("content"),
                                product_id: $("#product_id").val(),
                                customer_id: vendor_id,
                                vendor_type: vendor_type,
                                vendor_name: vendor_name,
                                amount: $("#total_amount_" + nozzle_id).val(),
                                quantity: $("#quantity_" + nozzle_id).val(),
                                rate: $("#sale_rate").val(),
                                notes: $("#notes").val(),
                                sale_date: $("#sale_date").val(),
                                selected_tank: $("#selected_tank_" + nozzle_id).val(),
                                nozzle_id: $("#nozzle_id_" + nozzle_id).val(),
                                opening_reading: $("#opening_reading_" + nozzle_id).val(),
                                closing_reading: $("#closing_reading_" + nozzle_id).val(),
                                test_sales: $("#testbox_" + nozzle_id).val(),
                            }
                        });
                    }
                });

                // If no nozzles to process, return early
                if (nozzlesToProcess.length === 0) {
                    Swal.fire({
                        icon: "info",
                        title: "Info",
                        text: "No nozzles to process",
                        confirmButtonColor: "#4154f1",
                    });
                    $("#add_sales_btn").attr("disabled", false).text("Submit");
                    return;
                }

                // Step 3: Show loader and send all requests
                Swal.fire({
                    title: "Processing...",
                    html: "Please wait while we process your requests.<br><small>Processing " + nozzlesToProcess.length + " nozzle(s)...</small>",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    // Convert all AJAX requests to Promises and execute them
                    var requestPromises = nozzlesToProcess.map(function (nozzle) {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                url: window.nozzleStoreUrl || "/sales/nozzle/store",
                                type: "POST",
                                data: nozzle.payload,
                                success: function (response) {
                                    resolve({
                                        success: true,
                                        response: response,
                                        nozzle_id: nozzle.nozzle_id
                                    });
                                },
                                error: function (xhr) {
                                    var errorMsg = "Failed to add sales for nozzle " + nozzle.nozzle_id;
                                    if (xhr && xhr.responseJSON) {
                                        if (xhr.responseJSON.error === "tank-limit-exceed") {
                                            errorMsg = "Tank stock is less than the stock you're selling";
                                        } else if (xhr.responseJSON.message) {
                                            errorMsg = xhr.responseJSON.message;
                                        }
                                    }
                                    resolve({
                                        success: false,
                                        error: errorMsg,
                                        nozzle_id: nozzle.nozzle_id
                                    });
                                }
                            });
                        });
                    });

                    // Wait for all requests to complete
                    var results = await Promise.all(requestPromises);

                    // Close the loader
                    Swal.close();

                    // Check if all requests were successful
                    var allSuccess = true;
                    var errorMessages = [];

                    results.forEach(function (result) {
                        if (!result.success || (result.response && !result.response.success)) {
                            allSuccess = false;
                            if (result.error) {
                                errorMessages.push(result.error);
                            } else if (result.response && result.response.message) {
                                errorMessages.push(result.response.message);
                            } else {
                                errorMessages.push("Failed to add sales for nozzle " + result.nozzle_id);
                            }
                        }
                    });

                    // Show appropriate message
                    if (allSuccess) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "All sales added successfully",
                            confirmButtonColor: "#4154f1",
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            html: "Some sales failed to add:<br><small>" + errorMessages.join("<br>") + "</small>",
                            confirmButtonColor: "#4154f1",
                        });
                        $("#add_sales_btn").attr("disabled", false).text("Submit");
                    }
                } catch (error) {
                    // Close the loader
                    Swal.close();
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An unexpected error occurred: " + (error.message || "Please try again"),
                        confirmButtonColor: "#4154f1",
                    });
                    $("#add_sales_btn").attr("disabled", false).text("Submit");
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Precheck failed",
                    confirmButtonColor: "#4154f1",
                });
                $("#add_sales_btn").attr("disabled", false).text("Submit");
            },
        });
    });

    // bottom print button
    $("#printReportBtn").on("click", function () {
        printSalesReport();
    });

    function printSalesReport() {
        const printWindow = window.open("", "_blank");
        const tableHTML = $("#salesTable")[0].outerHTML;
        const currentDate = new Date().toLocaleDateString();
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Nozzle Sales Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
                    .header h1 { margin: 0; color: #333; font-size: 24px; }
                    .header p { margin: 5px 0; color: #666; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                    th { background-color: #f5f5f5; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Nozzle Sales Report</h1>
                    <p>Generated on: ${currentDate}</p>
                </div>
                ${tableHTML}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

    // initialize defaults
    $("#product_id").val("");
    $("#tank_lari_id").val("");
    document.title = "Nozzle Sales";
});
