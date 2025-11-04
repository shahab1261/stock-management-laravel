$(document).ready(function () {
    // Initialize DataTables for all history tables
    $(".history-table").each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                processing: true,
                responsive: false,
                scrollX: true,
                searching: false, // Enable search box for history
                // Custom DOM: show entries left with margin, export buttons right
                dom: '<"row align-items-center"<"col-md-6 dt-left-margin"l><"col-md-6 d-flex justify-content-end"Bf>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"],
                ],
                pageLength: 25,
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

    // Handle delete sales button (history page)
    $(document).on('click', '.delete-sales-btn', function(e) {
        e.preventDefault();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var salesId = $(this).data('id');
        if (!salesId) {
            showError('Sales ID not found');
            return;
        }

        confirmDelete(function() {
            $.ajax({
                url: '/sales/delete',
                type: 'POST',
                data: { _token: csrfToken, sales_id: salesId },
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function(resp) {
                    if (resp && resp.success) {
                        showSuccess(resp.message || 'Sales deleted successfully');
                        setTimeout(function(){ location.reload(); }, 800);
                    } else {
                        showError((resp && resp.message) ? resp.message : 'Failed to delete sales, please try again');
                    }
                },
                error: function() {
                    showError('Failed to delete sales, please try again');
                }
            });
        });
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

    // Product filter change event
    $("#product_id").on("change", function () {
        // Auto-submit form when product selection changes
        if ($(this).closest('form').length) {
            // Optional: Add confirmation for auto-submit
            // $(this).closest('form').submit();
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

        // Show loading indicator
        showLoading();
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

    // Filter form quick actions
    $(".quick-filter").on("click", function(e) {
        e.preventDefault();
        var period = $(this).data('period');
        setQuickDateFilter(period);
    });

    // Advanced search toggle
    $("#advanced-search-toggle").on("click", function() {
        $("#advanced-search-section").slideToggle();
        var icon = $(this).find('i');
        if (icon.hasClass('bi-chevron-down')) {
            icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
        } else {
            icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
        }
    });
});

/**
 * Get export title based on current page
 */
function getExportTitle() {
    var pageTitle = document.title;
    var dateRange = getDateRangeFromForm();
    return pageTitle + " - " + dateRange;
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
 * Set quick date filter
 */
function setQuickDateFilter(period) {
    var today = new Date();
    var startDate, endDate;

    switch(period) {
        case 'today':
            startDate = endDate = formatDate(today);
            break;
        case 'yesterday':
            var yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = formatDate(yesterday);
            break;
        case 'week':
            var weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            startDate = formatDate(weekStart);
            endDate = formatDate(today);
            break;
        case 'month':
            var monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            startDate = formatDate(monthStart);
            endDate = formatDate(today);
            break;
        case 'year':
            var yearStart = new Date(today.getFullYear(), 0, 1);
            startDate = formatDate(yearStart);
            endDate = formatDate(today);
            break;
    }

    if (startDate && endDate) {
        $("#start_date").val(startDate);
        $("#end_date").val(endDate);

        // Auto-submit form
        $("form").first().submit();
    }
}

/**
 * Format date to YYYY-MM-DD
 */
function formatDate(date) {
    var d = new Date(date);
    var month = '' + (d.getMonth() + 1);
    var day = '' + d.getDate();
    var year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

/**
 * Format numbers for display
 */
function formatNumber(number) {
    return new Intl.NumberFormat("en-US").format(number);
}

/**
 * Format currency for display
 */
function formatCurrency(amount) {
    return 'Rs ' + new Intl.NumberFormat("en-US").format(amount);
}

/**
 * Show loading spinner
 */
function showLoading() {
    if (!$('.loading-overlay').length) {
        $('body').append(`
            <div class="loading-overlay">
                <div class="loading-spinner"></div>
            </div>
        `);
    }
}

/**
 * Hide loading spinner
 */
function hideLoading() {
    $('.loading-overlay').remove();
}

/**
 * Show success message
 */
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        timer: 3000,
        showConfirmButton: false
    });
}

/**
 * Show error message
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message
    });
}

/**
 * Show warning message
 */
function showWarning(message) {
    Swal.fire({
        icon: 'warning',
        title: 'Warning',
        text: message
    });
}

/**
 * Confirm delete action
 */
function confirmDelete(callback) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}

/**
 * Print table function
 */
function printTable(tableSelector) {
    var printWindow = window.open('', '_blank');
    var tableHtml = $(tableSelector).prop('outerHTML');

    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { font-size: 12px; }
                .table { font-size: 11px; }
                .badge { color: #000 !important; background: #fff !important; border: 1px solid #000; }
                @media print {
                    .table th, .table td { padding: 4px 2px; }
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            <div class="container-fluid">
                <h4 class="text-center mb-4">${getExportTitle()}</h4>
                ${tableHtml}
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
}

/**
 * Export table to CSV
 */
function exportToCSV(tableSelector, filename) {
    var csv = [];
    var rows = $(tableSelector + " tr");

    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = $(rows[i]).find("td, th");

        for (var j = 0; j < cols.length; j++) {
            var cellText = $(cols[j]).text().trim();
            // Clean up text and escape quotes
            cellText = cellText.replace(/"/g, '""');
            row.push('"' + cellText + '"');
        }

        csv.push(row.join(","));
    }

    // Download CSV
    var csvContent = csv.join("\n");
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement("a");

    if (link.download !== undefined) {
        var url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", filename || "export.csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Hide loading when page loads
$(window).on('load', function() {
    hideLoading();
});
