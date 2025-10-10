$(document).ready(function () {
    // Initialize DataTables for all ledger tables
    $(".history-table").each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                processing: true,
                responsive: false,
                scrollX: true,
                searching: false, // Disable search box for ledger tables
                // Custom DOM: show entries left with margin, export buttons right
                dom: '<"row align-items-center"<"col-md-6 dt-left-margin"l><"col-md-6 d-flex justify-content-end"Bf>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"],
                ],
                pageLength: 25,
                // Order by first column (Date) ascending for ledgers
                // order: [[0, "asc"]],
                buttons: [
                    {
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> Export to CSV',
                        className: 'btn btn-primary btn-sm mb-2 ms-2',
                        action: function (e, dt, button, config) {
                            exportLedgerToCSV();
                        }
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

    // Date filter functionality
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

    // Entity filter change event (product, supplier, customer, employee, etc.)
    $("#product_id, #supplier_id, #customer_id, #bank_id, #expense_id, #income_id, #employee_id").on("change", function () {
        // Auto-submit form when entity selection changes
        if ($(this).closest('form').length) {
            $(this).closest('form').submit();
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

    // Update document title based on selected entity
    updatePageTitle();

    // Initialize print button functionality
    initializePrintButton();

    // Add keyboard shortcuts
    initializeKeyboardShortcuts();
});

/**
 * Initialize print button functionality
 */
function initializePrintButton() {
    // Add print button if it doesn't exist
    if (!$('#printBtn').length) {
        const printButton = `
            <button type="button" id="printBtn" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-printer me-2"></i> Print
            </button>
        `;

        // Find the card header and add print button
        const cardHeader = $('.card-header .d-flex');
        if (cardHeader.length) {
            cardHeader.append(printButton);
        } else {
            // If no existing button container, create one
            $('.card-header').each(function() {
                if (!$(this).find('.d-flex').length) {
                    $(this).html(`
                        <div class="d-flex justify-content-between align-items-center">
                            ${$(this).html()}
                            ${printButton}
                        </div>
                    `);
                }
            });
        }
    }

    // Bind print button click event
    $('#printBtn').off('click').on('click', function() {
        printLedger();
    });
}

/**
 * Initialize keyboard shortcuts
 */
function initializeKeyboardShortcuts() {
    $(document).keydown(function(e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.which === 80) {
            e.preventDefault();
            printLedger();
        }

        // Ctrl+E for export
        if (e.ctrlKey && e.which === 69) {
            e.preventDefault();
            exportLedgerToCSV();
        }
    });
}

/**
 * Update page title based on selected entity
 */
function updatePageTitle() {
    var selectedText = "";
    var pageType = "";

    // Check which type of ledger page this is
    if ($("#product_id").length) {
        selectedText = $("#product_id option:selected").text();
        pageType = "Product Ledger";
    } else if ($("#supplier_id").length) {
        selectedText = $("#supplier_id option:selected").text();
        pageType = "Supplier Ledger";
    } else if ($("#customer_id").length) {
        selectedText = $("#customer_id option:selected").text();
        pageType = "Customer Ledger";
    } else if ($("#bank_id").length) {
        selectedText = $("#bank_id option:selected").text();
        pageType = "Bank Ledger";
    } else if ($("#expense_id").length) {
        selectedText = $("#expense_id option:selected").text();
        pageType = "Expense Ledger";
    } else if ($("#income_id").length) {
        selectedText = $("#income_id option:selected").text();
        pageType = "Income Ledger";
    } else {
        pageType = document.title; // For cash and MP ledgers
    }

    if (selectedText && !selectedText.startsWith("All")) {
        document.title = selectedText + " - " + pageType;
    } else {
        document.title = pageType;
    }
}

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
 * Print ledger function - Enhanced version similar to trial balance
 */
function printLedger() {
    const printWindow = window.open('', '_blank');
    const printContent = generateLedgerPrintContent();

    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

/**
 * Create complete ledger table HTML with footer
 */
function createCompleteLedgerTableHTML() {
    const table = $('#ledger_table')[0];
    const thead = table.querySelector('thead').outerHTML;
    const tbody = table.querySelector('tbody').outerHTML;

    // Calculate totals from the visible data
    let debitSum = 0;
    let creditSum = 0;

    $('#ledger_table tbody tr').each(function() {
        // Skip the opening balance row
        const isOpening = $(this).find('td:nth-child(2)').text().trim().toLowerCase() === 'opening balance'
            || $(this).find('td:nth-child(3)').text().trim().toLowerCase() === 'opening balance';
        if (isOpening) {
            return; // continue
        }

        const debitCell = $(this).find('td:nth-child(3)').text().trim();
        const creditCell = $(this).find('td:nth-child(4)').text().trim();

        // Extract numeric values, handling commas and dashes
        if (debitCell && debitCell !== '-' && !isNaN(debitCell.replace(/,/g, ''))) {
            const debitValue = parseFloat(debitCell.replace(/,/g, '')) || 0;
            debitSum += debitValue;
        }

        if (creditCell && creditCell !== '-' && !isNaN(creditCell.replace(/,/g, ''))) {
            const creditValue = parseFloat(creditCell.replace(/,/g, '')) || 0;
            creditSum += creditValue;
        }
    });

    // Create footer HTML
    const tfoot = `
        <tfoot class="table-light">
            <tr>
                <th class="text-center">-</th>
                <th>Total</th>
                <th class="text-center">${debitSum.toLocaleString()}</th>
                <th class="text-center">${creditSum.toLocaleString()}</th>
                <th class="text-center">-</th>
            </tr>
        </tfoot>
    `;

    return `
        <table class="table table-hover table-bordered history-table" style="width:100%">
            ${thead}
            ${tbody}
            ${tfoot}
        </table>
    `;
}

/**
 * Generate comprehensive print content for ledger
 */
function generateLedgerPrintContent() {
    const currentDate = new Date().toLocaleDateString();
    const startDate = $('#start_date').val() || 'N/A';
    const endDate = $('#end_date').val() || 'N/A';
    const ledgerType = getLedgerType();
    const selectedEntity = getSelectedEntity();

    // Create a complete table HTML with footer included
    const tableHTML = createCompleteLedgerTableHTML();

    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${ledgerType} Report</title>
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
                .entity-info {
                    margin-bottom: 15px;
                    text-align: center;
                    font-weight: bold;
                    background-color: #f8f9fa;
                    padding: 10px;
                    border-radius: 5px;
                }
                .date-range {
                    margin-bottom: 20px;
                    text-align: center;
                    font-weight: bold;
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
                tfoot {
                    display: table-footer-group;
                }
                tfoot tr {
                    background-color: #f8f9fa !important;
                    font-weight: bold;
                }
                .transaction-debit {
                    color: #dc3545;
                    font-weight: bold;
                }
                .transaction-credit {
                    color: #28a745;
                    font-weight: bold;
                }
                .badge {
                    font-size: 10px;
                    padding: 2px 6px;
                    border-radius: 3px;
                    font-weight: bold;
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
                <h1>${ledgerType}</h1>
                <p>Generated on: ${currentDate}</p>
            </div>
            ${selectedEntity ? `<div class="entity-info">${selectedEntity}</div>` : ''}
            <div class="date-range">
                Date Range: ${startDate} to ${endDate}
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
 * Get ledger type based on current page
 */
function getLedgerType() {
    const pageTitle = document.title;
    if (pageTitle.includes('Bank')) return 'Bank Ledger Report';
    if (pageTitle.includes('Cash')) return 'Cash Ledger Report';
    if (pageTitle.includes('Customer')) return 'Customer Ledger Report';
    if (pageTitle.includes('Supplier')) return 'Supplier Ledger Report';
    if (pageTitle.includes('Product')) return 'Product Ledger Report';
    if (pageTitle.includes('Employee')) return 'Employee Ledger Report';
    if (pageTitle.includes('Expense')) return 'Expense Ledger Report';
    if (pageTitle.includes('Income')) return 'Income Ledger Report';
    if (pageTitle.includes('MP')) return 'MP Ledger Report';
    return 'Ledger Report';
}

/**
 * Get selected entity name
 */
function getSelectedEntity() {
    let selectedText = '';

    if ($('#bank_id').length && $('#bank_id').val()) {
        selectedText = $('#bank_id option:selected').text();
    } else if ($('#customer_id').length && $('#customer_id').val()) {
        selectedText = $('#customer_id option:selected').text();
    } else if ($('#supplier_id').length && $('#supplier_id').val()) {
        selectedText = $('#supplier_id option:selected').text();
    } else if ($('#product_id').length && $('#product_id').val()) {
        selectedText = $('#product_id option:selected').text();
    } else if ($('#employee_id').length && $('#employee_id').val()) {
        selectedText = $('#employee_id option:selected').text();
    } else if ($('#expense_id').length && $('#expense_id').val()) {
        selectedText = $('#expense_id option:selected').text();
    } else if ($('#income_id').length && $('#income_id').val()) {
        selectedText = $('#income_id option:selected').text();
    }

    return selectedText && !selectedText.includes('All') ? `Selected: ${selectedText}` : '';
}

/**
 * Print table function - Legacy function for backward compatibility
 */
function printTable(tableSelector) {
    printLedger();
}

/**
 * Export ledger table to CSV with footer
 */
function exportLedgerToCSV() {
    var csv = [];
    var table = $('#ledger_table')[0];

    // Get table headers
    var headers = [];
    $(table.querySelectorAll('thead th')).each(function() {
        var headerText = $(this).text().trim();
        headerText = headerText.replace(/"/g, '""');
        headers.push('"' + headerText + '"');
    });
    csv.push(headers.join(','));

    // Get table body rows
    $(table.querySelectorAll('tbody tr')).each(function() {
        var row = [];
        $(this).find('td, th').each(function() {
            var cellText = $(this).text().trim();
            cellText = cellText.replace(/"/g, '""');
            row.push('"' + cellText + '"');
        });
        csv.push(row.join(','));
    });

    // Get footer row (total row)
    $(table.querySelectorAll('tfoot tr')).each(function() {
        var row = [];
        $(this).find('td, th').each(function() {
            var cellText = $(this).text().trim();
            cellText = cellText.replace(/"/g, '""');
            row.push('"' + cellText + '"');
        });
        csv.push(row.join(','));
    });

    // Download CSV
    var csvContent = csv.join("\n");
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement("a");

    var ledgerType = getLedgerType();
    var filename = ledgerType.replace(/\s+/g, '_') + '_' + new Date().toISOString().split('T')[0] + '.csv';

    if (link.download !== undefined) {
        var url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

/**
 * Export table to CSV (legacy function)
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

/**
 * Global print function for ledger pages
 * Can be called from anywhere to print current ledger
 */
window.LedgerPrint = {
    print: function() {
        printLedger();
    },

    export: function() {
        exportLedgerToCSV();
    },

    refresh: function() {
        window.location.reload();
    }
};
