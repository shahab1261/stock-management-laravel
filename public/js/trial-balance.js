/**
 * Trial Balance JavaScript
 * Handles trial balance page functionality
 */

$(document).ready(function() {
    // Initialize trial balance functionality
    initializeTrialBalance();

    /**
     * Initialize trial balance functionality
     */
    function initializeTrialBalance() {
        // Export button functionality
        $('#exportBtn').click(function() {
            exportTrialBalance();
        });

        // Print button functionality
        $('#printBtn').click(function() {
            printTrialBalance();
        });

        // Date range validation
        // $('#start_date, #end_date').change(function() {
        //     validateDateRange();
        // });

        // Add loading states
        $('form').submit(function() {
            validateDateRange();
            const submitBtn = $(this).find('button[type="submit"]');
            addLoadingState(submitBtn);
        });

        // Initialize tooltips
        initializeTooltips();

        // Add account type indicators
        addAccountTypeIndicators();
    }

    /**
     * Export trial balance to Excel
     */
    function exportTrialBalance() {
        const btn = $('#exportBtn');
        const originalText = btn.html();

        // Add loading state
        btn.prop('disabled', true);
        btn.html('<i class="bi bi-hourglass-split me-2"></i>Exporting...');

        // Create export data
        const tableData = [];
        const table = document.getElementById('trialBalanceTable');

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
        downloadCSV(csvContent, 'trial_balance_' + new Date().toISOString().split('T')[0] + '.csv');

        // Reset button
        setTimeout(() => {
            btn.prop('disabled', false);
            btn.html(originalText);
        }, 2000);
    }

    /**
     * Print trial balance
     */
    function printTrialBalance() {
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

        const tableHTML = $('#trialBalanceTable')[0].outerHTML;

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Trial Balance Report</title>
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
                    .table-light {
                        background-color: #f8f9fa;
                    }
                    .table-primary {
                        background-color: #e3f2fd;
                    }
                    .table-warning {
                        background-color: #fff3cd;
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
                    <h1>Trial Balance Report</h1>
                    <p>Generated on: ${currentDate}</p>
                </div>
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
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());

        if (startDate && endDate && startDate > endDate) {
            showAlert('error', 'Start date cannot be later than end date');
            $('#end_date').val('');
        }
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
        // Add tooltips to account types
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Add custom tooltips for balance amounts
        $('.text-success, .text-danger').each(function() {
            const amount = $(this).text();
            if (amount && amount !== '-') {
                $(this).attr('title', 'Amount: ' + amount);
                $(this).tooltip();
            }
        });
    }

    /**
     * Add account type indicators
     */
    function addAccountTypeIndicators() {
        $('tbody tr').each(function() {
            const accountType = $(this).find('td:nth-child(2)').text().toLowerCase();

            switch(accountType) {
                case 'supplier':
                    $(this).addClass('account-type-supplier');
                    break;
                case 'customer':
                    $(this).addClass('account-type-customer');
                    break;
                case 'product':
                    $(this).addClass('account-type-product');
                    break;
                case 'expense':
                    $(this).addClass('account-type-expense');
                    break;
                case 'income':
                    $(this).addClass('account-type-income');
                    break;
                case 'bank':
                    $(this).addClass('account-type-bank');
                    break;
                case 'cash':
                    $(this).addClass('account-type-cash');
                    break;
                case 'mp':
                    $(this).addClass('account-type-mp');
                    break;
                case 'employee':
                    $(this).addClass('account-type-employee');
                    break;
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
     * Calculate balance totals
     */
    function calculateBalanceTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        $('tbody tr:not(.table-light):not(.table-primary):not(.table-warning)').each(function() {
            const debitText = $(this).find('td:nth-child(4)').text().replace(/[^\d.-]/g, '');
            const creditText = $(this).find('td:nth-child(5)').text().replace(/[^\d.-]/g, '');

            if (debitText && debitText !== '-') {
                totalDebit += parseFloat(debitText) || 0;
            }

            if (creditText && creditText !== '-') {
                totalCredit += parseFloat(creditText) || 0;
            }
        });

        return {
            debit: totalDebit,
            credit: totalCredit,
            difference: Math.abs(totalDebit - totalCredit)
        };
    }

    /**
     * Refresh trial balance data
     */
    function refreshTrialBalance() {
        const currentUrl = new URL(window.location);
        window.location.reload();
    }

    /**
     * Auto-refresh functionality (optional)
     */
    function enableAutoRefresh(intervalMinutes = 5) {
        setInterval(refreshTrialBalance, intervalMinutes * 60 * 1000);
    }

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.which === 80) {
            e.preventDefault();
            printTrialBalance();
        }

        // Ctrl+E for export
        if (e.ctrlKey && e.which === 69) {
            e.preventDefault();
            exportTrialBalance();
        }

        // F5 for refresh (default behavior)
        if (e.which === 116) {
            // Allow default F5 behavior
            return true;
        }
    });

    // Window resize handler for responsive tables
    $(window).resize(function() {
        // Adjust table responsiveness if needed
        if ($.fn.DataTable.isDataTable('#trialBalanceTable')) {
            $('#trialBalanceTable').DataTable().columns.adjust().responsive.recalc();
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
});

/**
 * Global functions that can be called from outside
 */
window.TrialBalance = {
    export: function() {
        $('#exportBtn').click();
    },

    print: function() {
        $('#printBtn').click();
    },

    refresh: function() {
        window.location.reload();
    }
};
