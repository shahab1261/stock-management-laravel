$(document).ready(function () {
    // Initialize DataTables for all daybook tables
    $(".daybook-table").each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                processing: true,
                responsive: false,
                scrollX: true,
                searching: false, // Disable search box
                // Custom DOM: show entries left with margin, export buttons right
                dom: '<"row align-items-center"<"col-md-6 dt-left-margin"l><"col-md-6 d-flex justify-content-end"B>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100],
                ],
                pageLength: 10,
                // Order by first column (ID) descending
                order: [[0, "desc"]],
                buttons: [
                    {
                        extend: 'csvHtml5',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> Export to CSV',
                        className: 'btn btn-primary btn-sm mb-2 ms-2'
                    }
                ],
                drawCallback: function () {
                    // Reinitialize tooltips after table redraw
                    $('[data-bs-toggle="tooltip"]').tooltip("dispose");
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
            });

            // Add left margin to the show entries dropdown
            $('.dt-left-margin').css('padding-left', '15px');
        }
    });

    // Tab switching - recalculate DataTable columns
    $('[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        setTimeout(function () {
            $.fn.dataTable
                .tables({ visible: true, api: true })
                .columns.adjust();
        }, 100);
    });

    // Date filter functionality
    $("#start_date, #end_date").on("change", function () {
        var startDate = $("#start_date").val();
        var endDate = $("#end_date").val();

        if (startDate && endDate) {
            if (new Date(startDate) > new Date(endDate)) {
                // Swal.fire({
                //     icon: "warning",
                //     title: "Invalid Date Range",
                //     text: "Start date cannot be greater than end date.",
                // });
                // return false;
            }
        }
    });

    // Form submission validation
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

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Responsive table handling for mobile
    $(window).on("resize", function () {
        setTimeout(function () {
            $.fn.dataTable
                .tables({ visible: true, api: true })
                .columns.adjust();
        }, 100);
    });
});

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
