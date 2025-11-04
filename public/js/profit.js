/**
 * Profit & Loss JavaScript
 * Handles profit and loss page functionality
 */

$(document).ready(function() {
    // Initialize profit functionality
    initializeProfitPage();

    /**
     * Initialize profit page functionality
     */
    function initializeProfitPage() {
        // Export button functionality
        $('#exportBtn').click(function() {
            exportProfitStatement();
        });

        // Print button functionality
        $('#printBtn').click(function() {
            printProfitStatement();
        });

        // Update rates button functionality
        $('#updateRatesBtn').click(function() {
            updateProductRates();
        });

        // Date range validation
        $('#start_date, #end_date').change(function() {
            validateDateRange();
        });

        // Add loading states
        $('form').submit(function() {
            const submitBtn = $(this).find('button[type="submit"]');
            addLoadingState(submitBtn);
        });

        // Initialize tooltips
        initializeTooltips();

        // Add amount formatting
        formatAmounts();
    }

    /**
     * Export profit statement to Excel
     */
    function exportProfitStatement() {
        const btn = $('#exportBtn');
        const originalText = btn.html();

        // Add loading state
        btn.prop('disabled', true);
        btn.html('<i class="bi bi-hourglass-split me-1"></i>Exporting...');

        // Create export data
        const tableData = [];
        const table = document.getElementById('profitTable');

        // Get table headers
        const headers = [];
        $(table).find('thead th').each(function() {
            headers.push($(this).text().trim());
        });
        tableData.push(headers);

        // Get table data
        $(table).find('tbody tr').each(function() {
            const row = [];
            $(this).find('td').each(function() {
                let cellText = $(this).text().trim();
                // Clean up the text (remove extra spaces and line breaks)
                cellText = cellText.replace(/\s+/g, ' ').trim();
                row.push(cellText);
            });
            if (row.length > 0) {
                tableData.push(row);
            }
        });

        // Convert to CSV and download
        const csvContent = convertToCSV(tableData);
        downloadCSV(csvContent, 'profit_statement_' + new Date().toISOString().split('T')[0] + '.csv');

        // Reset button
        setTimeout(() => {
            btn.prop('disabled', false);
            btn.html(originalText);
        }, 2000);
    }

    /**
     * Print profit statement
     */
    function printProfitStatement() {
        const printWindow = window.open('', '_blank');
        const printContent = generatePrintContent();

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }

    /**
     * Generate print content
     */
    function generatePrintContent() {
        const currentDate = new Date().toLocaleDateString();
        const startDate = $('#start_date').val() || 'N/A';
        const endDate = $('#end_date').val() || 'N/A';

        const tableHTML = $('#profitTable')[0].outerHTML;

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Profit & Loss Statement</title>
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
                    .table-success {
                        background-color: #d4edda;
                    }
                    .table-info {
                        background-color: #d1ecf1;
                    }
                    .table-warning {
                        background-color: #fff3cd;
                    }
                    .table-primary {
                        background-color: #cce5ff;
                        font-weight: bold;
                    }
                    .table-light {
                        background-color: #f8f9fa;
                    }
                    .text-success {
                        color: #28a745;
                    }
                    .text-danger {
                        color: #dc3545;
                    }
                    .text-info {
                        color: #17a2b8;
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
                    <h1>Profit & Loss Statement</h1>
                    <p>Generated on: ${currentDate}</p>
                </div>
                <div class="date-range">
                    Period: ${startDate} to ${endDate}
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
     * Update product rates (confirm + AJAX)
     */
    function updateProductRates() {
        const btn = $('#updateRatesBtn');
        const url = btn.data('url');

        if (!url) {
            showAlert('error', 'Update URL not found.');
            return;
        }

        Swal.fire({
            text: 'Are you sure to update the purchase rate of all products as current rate?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes, update',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#4154f1',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Updating...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {},
                    success: function (resp) {
                        if (resp && resp.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Rate updated successfully!'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: (resp && resp.message) ? resp.message : 'Please try again!'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Request failed. Please try again!'
                        });
                    },
                    complete: function () {
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });
            }
        });
    }

    /**
     * Convert array to CSV format
     */
    function convertToCSV(data) {
        return data.map(row =>
            row.map(cell => {
                // Handle cells that contain commas or quotes
                if (typeof cell === 'string' && (cell.includes(',') || cell.includes('"'))) {
                    return `"${cell.replace(/"/g, '""')}"`;
                }
                return cell;
            }).join(',')
        ).join('\n');
    }

    /**
     * Download CSV file
     */
    function downloadCSV(csvContent, filename) {
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');

        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    /**
     * Validate date range
     */
    function validateDateRange() {
        // const startDate = new Date($('#start_date').val());
        // const endDate = new Date($('#end_date').val());

        // if (startDate && endDate && startDate > endDate) {
        //     showAlert('error', 'Start date cannot be later than end date');
        //     $('#end_date').val('');
        // }
    }

    /**
     * Add loading state to button
     */
    function addLoadingState(button) {
        const originalText = button.html();
        button.prop('disabled', true);
        button.html('<i class="bi bi-hourglass-split me-2"></i>Loading...');

        // Reset after form submission
        setTimeout(() => {
            button.prop('disabled', false);
            button.html(originalText);
        }, 3000);
    }

    /**
     * Initialize tooltips
     */
    function initializeTooltips() {
        // Add tooltips to amounts
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Add custom tooltips for profit amounts
        $('.profit-positive, .profit-negative').each(function() {
            const amount = $(this).text();
            if (amount && amount !== '-') {
                $(this).attr('title', 'Amount: ' + amount);
                $(this).tooltip();
            }
        });
    }

    /**
     * Format amounts with proper styling
     */
    function formatAmounts() {
        $('tbody tr').each(function() {
            const amountCell = $(this).find('td:last-child');
            const amountText = amountCell.text().trim();

            if (amountText.includes('Rs ') && amountText !== 'Rs 0') {
                const amount = parseFloat(amountText.replace(/[^\d.-]/g, ''));

                if (amount > 0) {
                    amountCell.addClass('amount-positive');
                } else if (amount < 0) {
                    amountCell.addClass('amount-negative');
                } else {
                    amountCell.addClass('amount-neutral');
                }
            }
        });
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        const icon = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';

        Swal.fire({
            title: type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Info',
            text: message,
            icon: icon,
            confirmButtonColor: '#4154f1',
            confirmButtonText: 'OK'
        });
    }

    /**
     * Format numbers for display
     */
    function formatNumber(number, decimals = 2) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    }

    /**
     * Calculate profit margins
     */
    function calculateProfitMargins() {
        // This can be used to calculate various profit margins
        const grossProfit = parseFloat($('.table-success td:last-child').text().replace(/[^\d.-]/g, '')) || 0;
        const netProfit = parseFloat($('.table-primary td:last-child').text().replace(/[^\d.-]/g, '')) || 0;

        return {
            gross: grossProfit,
            net: netProfit,
            margin: grossProfit > 0 ? (netProfit / grossProfit) * 100 : 0
        };
    }

    /**
     * Refresh profit data
     */
    function refreshProfitData() {
        window.location.reload();
    }

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.which === 80) {
            e.preventDefault();
            printProfitStatement();
        }

        // Ctrl+E for export
        if (e.ctrlKey && e.which === 69) {
            e.preventDefault();
            exportProfitStatement();
        }

        // F5 for refresh (default behavior)
        if (e.which === 116) {
            // Allow default F5 behavior
            return true;
        }
    });

    // Window resize handler for responsive tables
    $(window).resize(function() {
        if ($.fn.DataTable.isDataTable('#profitTable')) {
            $('#profitTable').DataTable().columns.adjust().responsive.recalc();
        }
    });

    // Add print media query detection
    if (window.matchMedia) {
        const mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener(function(mql) {
            if (mql.matches) {
                // Before print
                $('body').addClass('printing');
            } else {
                // After print
                $('body').removeClass('printing');
            }
        });
    }

    // Auto-calculate totals when data changes (if needed)
    function recalculateTotals() {
        // This function can be used to recalculate totals dynamically
        // if the data changes through user interaction
    }
});

/**
 * Global functions that can be called from outside
 */
window.ProfitPage = {
    export: function() {
        $('#exportBtn').click();
    },

    print: function() {
        $('#printBtn').click();
    },

    refresh: function() {
        window.location.reload();
    },

    updateRates: function() {
        $('#updateRatesBtn').click();
    }
};
