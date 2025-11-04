/**
 * Wet Stock Analysis JavaScript
 * Handles wet stock analysis functionality
 */

$(document).ready(function() {
    // Initialize wet stock functionality
    initializeWetStockPage();

    /**
     * Initialize wet stock page functionality
     */
    function initializeWetStockPage() {
        // Export button functionality
        $('#exportBtn').click(function() {
            exportWetStockData();
        });

        // Print button functionality
        $('#printBtn').click(function() {
            printWetStockReport();
        });

        // Form submission with loading state
        $('form').submit(function() {
            const submitBtn = $(this).find('button[type="submit"]');
            addLoadingState(submitBtn);
        });

        // Initialize tooltips for variance badges
        initializeTooltips();

        // Add variance color coding
        addVarianceColorCoding();

        // Initialize date range validation
        initializeDateValidation();
    }

    /**
     * Export wet stock data to CSV
     */
    function exportWetStockData() {
        const btn = $('#exportBtn');
        const originalText = btn.html();

        // Add loading state
        btn.prop('disabled', true);
        btn.html('<i class="bi bi-hourglass-split me-1"></i>Exporting...');

        // Build export URL with current filters
        const params = new URLSearchParams(window.filterParams);
        const exportUrl = `${window.routes.export}?${params.toString()}`;

        // Fetch export data
        $.get(exportUrl)
            .done(function(response) {
                if (response.success) {
                    // Convert data to CSV and download
                    const csvContent = convertToCSV(response.data);
                    downloadCSV(csvContent, response.filename);
                    showAlert('success', 'Data exported successfully');
                } else {
                    showAlert('error', 'Failed to export data');
                }
            })
            .fail(function() {
                showAlert('error', 'Error occurred during export');
            })
            .always(function() {
                // Reset button
                btn.prop('disabled', false);
                btn.html(originalText);
            });
    }

    /**
     * Print wet stock report
     */
    function printWetStockReport() {
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
        const startDate = window.filterParams.start_date || 'N/A';
        const endDate = window.filterParams.end_date || 'N/A';
        const tankFilter = window.filterParams.tank_id ?
            $(`#tank_id option[value="${window.filterParams.tank_id}"]`).text() : 'All Tanks';

        const tableHTML = $('#wetStockTable')[0].outerHTML;

        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Wet Stock Analysis Report</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                        font-size: 11px;
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
                    .filters {
                        margin-bottom: 20px;
                        background: #f5f5f5;
                        padding: 10px;
                        border-radius: 5px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                        font-size: 9px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 4px;
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
                    .table-warning {
                        background-color: #fff3cd;
                        font-weight: bold;
                    }
                    .text-success {
                        color: #28a745;
                    }
                    .text-danger {
                        color: #dc3545;
                    }
                    .badge {
                        padding: 2px 6px;
                        border-radius: 12px;
                        font-size: 8px;
                        font-weight: bold;
                    }
                    .bg-success {
                        background-color: #28a745;
                        color: white;
                    }
                    .bg-warning {
                        background-color: #ffc107;
                        color: black;
                    }
                    .bg-danger {
                        background-color: #dc3545;
                        color: white;
                    }
                    .bg-info {
                        background-color: #17a2b8;
                        color: white;
                    }
                    .footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 8px;
                        color: #666;
                        border-top: 1px solid #ddd;
                        padding-top: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Wet Stock Analysis Report</h1>
                    <p>Generated on: ${currentDate}</p>
                </div>
                <div class="filters">
                    <strong>Filters Applied:</strong><br>
                    Tank: ${tankFilter} |
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
     * Convert data array to CSV format
     */
    function convertToCSV(data) {
        if (!data || data.length === 0) return '';

        const headers = [
            'Date', 'Tank', 'Opening Stock', 'Purchase', 'Sales',
            'Book Stock', 'Dip Value (mm)', 'Dip Stock', 'Gain/Loss',
            'Total Gain/Loss', 'Variance (%)', 'Cumulative Sale', 'Cumulative Variance (%)'
        ];

        const csvRows = [headers.join(',')];

        data.forEach(row => {
            const values = [
                row.date ? new Date(row.date).toLocaleDateString() : '-',
                row.tank_name || '-',
                row.opening_stock !== null ? row.opening_stock : '-',
                row.purchase_stock !== null ? row.purchase_stock : '-',
                row.sales_stock !== null ? row.sales_stock : '-',
                row.book_stock !== null ? row.book_stock : '-',
                row.dip_value !== null ? row.dip_value : '-',
                row.dip_stock !== null ? row.dip_stock : '-',
                row.gain_loss !== null ? row.gain_loss : '-',
                row.total_gain_loss !== null ? row.total_gain_loss : '-',
                row.variance !== null ? row.variance.toFixed(2) : '-',
                row.cumulative_sale !== null ? row.cumulative_sale : '-',
                row.cumulative_variance !== null ? row.cumulative_variance.toFixed(2) : '-'
            ];

            // Handle values that contain commas or quotes
            const escapedValues = values.map(value => {
                if (typeof value === 'string' && (value.includes(',') || value.includes('"'))) {
                    return `"${value.replace(/"/g, '""')}"`;
                }
                return value;
            });

            csvRows.push(escapedValues.join(','));
        });

        return csvRows.join('\n');
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
        // Add tooltips to variance badges
        $('.badge').each(function() {
            const variance = parseFloat($(this).text());
            if (!isNaN(variance)) {
                let tooltipText = '';
                if (Math.abs(variance) <= 2) {
                    tooltipText = 'Excellent variance (≤2%)';
                } else if (Math.abs(variance) <= 5) {
                    tooltipText = 'Acceptable variance (≤5%)';
                } else {
                    tooltipText = 'High variance (>5%) - needs attention';
                }

                $(this).attr('title', tooltipText);
                $(this).tooltip();
            }
        });

        // Add tooltips to gain/loss values
        $('.text-success, .text-danger').each(function() {
            const value = parseFloat($(this).text().replace(/,/g, ''));
            if (!isNaN(value)) {
                const tooltipText = value >= 0 ? 'Gain in stock' : 'Loss in stock';
                $(this).attr('title', tooltipText);
                $(this).tooltip();
            }
        });
    }

    /**
     * Add variance color coding
     */
    function addVarianceColorCoding() {
        $('.badge').each(function() {
            const text = $(this).text();
            const variance = parseFloat(text);

            if (!isNaN(variance)) {
                const absVariance = Math.abs(variance);

                // Remove existing classes
                $(this).removeClass('bg-success bg-warning bg-danger');

                // Add appropriate class based on variance
                if (absVariance <= 2) {
                    $(this).addClass('bg-success');
                } else if (absVariance <= 5) {
                    $(this).addClass('bg-warning');
                } else {
                    $(this).addClass('bg-danger');
                }
            }
        });
    }

    /**
     * Initialize date range validation
     */
    function initializeDateValidation() {
        $('#start_date, #end_date').change(function() {
            // const startDate = new Date($('#start_date').val());
            // const endDate = new Date($('#end_date').val());

            // if (startDate && endDate && startDate > endDate) {
            //     showAlert('warning', 'Start date cannot be later than end date');
            //     $('#end_date').val('');
            // }
        });
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        const icon = type === 'success' ? 'success' : type === 'error' ? 'error' : 'warning';

        Swal.fire({
            title: type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Warning!',
            text: message,
            icon: icon,
            confirmButtonColor: '#4154f1',
            confirmButtonText: 'OK'
        });
    }

    /**
     * Format number for display
     */
    function formatNumber(number, decimals = 2) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    }

    /**
     * Calculate summary statistics
     */
    function calculateSummaryStats() {
        if (!window.wetStockData || window.wetStockData.length === 0) return;

        const dataRows = window.wetStockData.filter(row => row.tank_name !== 'TOTAL');

        const stats = {
            totalRecords: dataRows.length,
            totalGainLoss: dataRows.reduce((sum, row) => sum + (row.total_gain_loss || 0), 0),
            avgVariance: 0,
            excellentVariance: 0,
            acceptableVariance: 0,
            poorVariance: 0
        };

        // Calculate variance statistics
        const variances = dataRows.filter(row => row.variance !== null).map(row => row.variance);
        if (variances.length > 0) {
            stats.avgVariance = variances.reduce((sum, v) => sum + v, 0) / variances.length;
            stats.excellentVariance = variances.filter(v => Math.abs(v) <= 2).length;
            stats.acceptableVariance = variances.filter(v => Math.abs(v) > 2 && Math.abs(v) <= 5).length;
            stats.poorVariance = variances.filter(v => Math.abs(v) > 5).length;
        }

        return stats;
    }

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+E for export
        if (e.ctrlKey && e.which === 69) {
            e.preventDefault();
            $('#exportBtn').click();
        }

        // Ctrl+P for print
        if (e.ctrlKey && e.which === 80) {
            e.preventDefault();
            $('#printBtn').click();
        }
    });

    // Window resize handler for responsive tables
    $(window).resize(function() {
        if ($.fn.DataTable.isDataTable('#wetStockTable')) {
            $('#wetStockTable').DataTable().columns.adjust().responsive.recalc();
        }
    });

    // Auto-calculate and display additional insights
    function displayInsights() {
        const stats = calculateSummaryStats();
        if (!stats) return;

        // You can add insight cards or notifications here
        // For example, alert if variance is consistently high
        if (stats.poorVariance / stats.totalRecords > 0.5) {
            console.log('High variance detected in over 50% of records');
        }
    }

    // Initialize insights
    displayInsights();
});

/**
 * Global functions that can be called from outside
 */
window.WetStockPage = {
    export: function() {
        $('#exportBtn').click();
    },

    print: function() {
        $('#printBtn').click();
    },

    refresh: function() {
        window.location.reload();
    },

    filterByTank: function(tankId) {
        $('#tank_id').val(tankId);
        $('form').submit();
    }
};
