$(document).ready(function () {
    // Initialize DataTables for all report tables
    $(".daybook-table, .history-table, .reports-table").each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                processing: true,
                responsive: false,
                scrollX: true,
                dom: '<"row align-items-center"<"col-md-6"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100],
                ],
                pageLength: 10,
                order: [
                    [0, "desc"]
                ],
            });

            $(".dt-left-margin").css("padding-left", "15px");
        }
    });

    $('[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        setTimeout(function () {
            $.fn.dataTable
                .tables({ visible: true, api: true })
                .columns.adjust();
        }, 100);
    });

    $("#start_date, #end_date").on("change", function () {
        var startDate = $("#start_date").val();
        var endDate = $("#end_date").val();

        if (startDate && endDate) {
            if (new Date(startDate) > new Date(endDate)) {
                Swal.fire({
                    icon: "warning",
                    title: "Invalid Date Range",
                    text: "Start date cannot be greater than end date.",
                });
                return false;
            }
        }
    });

    $("form").on("submit", function (e) {
        var startDate = $("#start_date").val();
        var endDate = $("#end_date").val();

        if (startDate && endDate) {
            if (new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Invalid Date Range",
                    text: "Start date cannot be greater than end date.",
                });
                return false;
            }
        }
    });

    $('[data-bs-toggle="tooltip"]').tooltip();

    $(window).on("resize", function () {
        setTimeout(function () {
            $.fn.dataTable
                .tables({ visible: true, api: true })
                .columns.adjust();
        }, 100);
    });

    $("#vendor_dropdown").on("change", function () {
        var selectedOption = $(this).find("option:selected");
        var vendorId = selectedOption.val();
        var vendorType = selectedOption.data("type");
        var vendorName = selectedOption.data("name");

        $("#vendor_id").val(vendorId);
        $("#vendor_type").val(vendorType);
        $("#vendor_name").val(vendorName);
    });

    $("#vendor_dropdown").on("change", function () {
        if ($(this).val()) {
            setTimeout(function () {
                $("form").first().submit();
            }, 500);
        }
    });
});

/**
 * Get report title based on current page
 */
function getReportTitle() {
    var title = document.title;
    if (title.includes("Account History")) return "Account_History_Report";
    if (title.includes("All Stocks")) return "All_Stocks_Report";
    if (title.includes("Summary")) return "Summary_Report";
    if (title.includes("Purchase Transport"))
        return "Purchase_Transport_Report";
    if (title.includes("Sale Transport")) return "Sale_Transport_Report";
    return "Report";
}

/**
 * Get date range from form inputs
 */
function getDateRangeFromForm() {
    var startDate = $("#start_date").val();
    var endDate = $("#end_date").val();

    if (startDate && endDate) {
        var start = new Date(startDate).toLocaleDateString();
        var end = new Date(endDate).toLocaleDateString();
        return `${start} to ${end}`;
    }

    return "Current Date";
}

/**
 * Format numbers for display
 */
function formatNumber(number) {
    return new Intl.NumberFormat("en-US").format(number);
}

/**
 * Show loading spinner
 */
function showLoading() {
    Swal.fire({
        title: "Loading...",
        text: "Please wait while we prepare your report",
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        },
    });
}

/**
 * Hide loading spinner
 */
function hideLoading() {
    Swal.close();
}

/**
 * View chamber details function (Purchase Transport Report)
 */
function viewChambers(purchaseId) {
    $("#chamberModal").modal("show");

    // Reset content
    $("#chamberContent").html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);

    // Make AJAX request
    $.ajax({
        url: window.chamberDataUrl || "/reports/chamber-data",
        type: "POST",
        data: {
            id: purchaseId,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.product_list && response.product_list.length > 0) {
                let html = '<div class="table-responsive">';
                html += '<table class="table table-striped" id="purchase_table1">';
                html += '<thead class="table-light">';
                html += '<tr>';
                html += '<th style="font-size: 11px;">Chamber #</th>';
                html += '<th style="font-size: 11px;">Capacity (ltr)</th>';
                html += '<th style="font-size: 11px;">Dip</th>';
                html += '<th style="font-size: 11px;">Rec. Dip</th>';
                html += '<th style="font-size: 11px;">Gain/Loss</th>';
                html += '<th style="font-size: 11px;">Ltr</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody id="chamber_table_body">';

                response.product_list.forEach(function (item, index) {
                    html += '<tr>';
                    html += '<td>' + (index + 1) + '</td>';
                    html += '<td>' + (item.capacity ?? '-') + '</td>';
                    html += '<td>' + (item.dip_value ?? '-') + '</td>';
                    html += '<td>' + (item.rec_dip_value ?? '-') + '</td>';
                    html += '<td>' + (item.gain_loss ?? '-') + '</td>';
                    html += '<td>' + (item.dip_liters ?? '-') + '</td>';
                    html += '</tr>';
                });

                html += '</tbody>';
                html += '</table>';

                // Measurements block like old project
                var measurementsRaw = response.product_list[0].measurements || '';
                if (measurementsRaw) {
                    var m = (measurementsRaw + '').split('_');
                    html += '<div id="measurements_div" class="mt-2">';
                    html += '<span><strong> Product: </strong> ' + (m[0] || '-') + '</span><br>';
                    html += '<span><strong> Invoice.Temp: </strong> ' + (m[1] || '-') + '</span><br>';
                    html += '<span><strong> Rec. Temp: </strong> ' + (m[2] || '-') + '</span><br>';
                    html += '<span><strong> Temp Loss/Gain: </strong> ' + (m[3] || '-') + '</span><br>';
                    html += '<span><strong> Dip Loss/Gain Ltr: </strong> ' + (m[4] || '-') + '</span><br>';
                    html += '<span><strong> Loss/Gain <small>by temperature</small>: </strong> ' + (m[5] || '-') + '</span><br>';
                    html += '<span><strong> <small>Actual Short</small> Loss/Gain: </strong> ' + (m[6] || '-') + '</span><br>';
                    html += '</div>';
                } else {
                    html += '<div id="message_div" class="text-center text-danger">No Measurements Found</div>';
                }

                $("#chamberContent").html(html);
            } else {
                $("#chamberContent").html(
                    '<div class="alert alert-info text-center">No chamber details found for this purchase.</div>'
                );
            }
        },
        error: function () {
            $("#chamberContent").html(
                '<div class="alert alert-danger text-center">Error loading chamber details. Please try again.</div>'
            );
        },
    });
}

/**
 * Initialize Select2 for better dropdown experience
 */
function initializeSelect2() {
    if ($.fn.select2) {
        $(".select2").select2({
            theme: "bootstrap-5",
            width: "100%",
        });
    }
}

/**
 * Reset all filters
 */
function resetFilters() {
    $("form")[0].reset();
    $(".select2").val(null).trigger("change");
    window.location.href = window.location.pathname;
}

/**
 * Print specific section
 */
function printSection(sectionId) {
    var printContents = document.getElementById(sectionId).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
