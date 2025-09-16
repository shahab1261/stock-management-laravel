/**
 * Billing Page JavaScript
 */

$(document).ready(function() {
    // Initialize billing functionality
    initializeBilling();
});

function initializeBilling() {
    // Handle vendor dropdown change (same as old project)
    $('#vendor_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const vendorType = selectedOption.data('type');
        $('#vendor_type').val(vendorType || '');
    });

    // Initialize vendor selection (matching old project selectOption function)
    if (window.filterParams && window.filterParams.vendor_id && window.filterParams.vendor_type) {
        selectOption('vendor_id', window.filterParams.vendor_id, window.filterParams.vendor_type);
    }

    // Handle export button
    $('#exportBtn').on('click', function() {
        exportBillingData();
    });

    // Handle print button
    $('#printBtn').on('click', function() {
        printBillingReport();
    });

    // Auto-submit form on filter change (optional)
    $('.auto-submit').on('change', function() {
        $(this).closest('form').submit();
    });
}

/**
 * Export billing data to CSV
 */
function exportBillingData() {
    // Show loading state
    $('#exportBtn').addClass('loading').prop('disabled', true);

    // Build export URL with current filters
    const params = new URLSearchParams();

    // Add filter parameters
    if (window.filterParams) {
        Object.keys(window.filterParams).forEach(key => {
            if (window.filterParams[key]) {
                params.append(key, window.filterParams[key]);
            }
        });
    }

    // Create export URL
    const exportUrl = window.routes.export + '?' + params.toString();

    // Create temporary link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = `billing_report_${getCurrentDate()}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Remove loading state
    setTimeout(() => {
        $('#exportBtn').removeClass('loading').prop('disabled', false);
    }, 1000);
}

/**
 * Print billing report
 */
function printBillingReport() {
    // Create print content
    const printContent = createPrintContent();

    // Open print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();

    // Wait for content to load then print
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}

/**
 * Create print content
 */
function createPrintContent() {
    const title = 'Credit Sale Transport Report';
    const dateRange = `From ${window.filterParams.start_date} to ${window.filterParams.end_date}`;

    let content = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${title}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .header h1 { margin: 0; color: #333; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .text-center { text-align: center; }
                .text-end { text-align: right; }
                .summary-section { margin-bottom: 40px; }
                .section-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #333; }
                .total-row { background-color: #e9ecef; font-weight: bold; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>${title}</h1>
                <p>${dateRange}</p>
            </div>
    `;

    // Add summary table
    content += `
        <div class="summary-section">
            <div class="section-title">Summary</div>
            <table>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Vendor</th>
                        <th>Vehicle No.</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Get summary table data
    $('#summaryTable tbody tr').each(function(index) {
        if (!$(this).hasClass('table-primary')) {
            content += '<tr>';
            $(this).find('td').each(function() {
                const cellText = $(this).text().trim();
                const cellClass = $(this).hasClass('text-center') ? ' class="text-center"' :
                                 $(this).hasClass('text-end') ? ' class="text-end"' : '';
                content += `<td${cellClass}>${cellText}</td>`;
            });
            content += '</tr>';
        } else {
            content += '<tr class="total-row">';
            $(this).find('td').each(function() {
                const cellText = $(this).text().trim();
                const cellClass = $(this).hasClass('text-center') ? ' class="text-center"' :
                                 $(this).hasClass('text-end') ? ' class="text-end"' : '';
                content += `<td${cellClass}>${cellText}</td>`;
            });
            content += '</tr>';
        }
    });

    content += `
                </tbody>
            </table>
        </div>
    `;

    // Add details table
    content += `
        <div class="details-section">
            <div class="section-title">Details</div>
            <table>
                <thead>
                    <tr>
                        <th class="text-center">Date</th>
                        <th>Vendor</th>
                        <th>Product</th>
                        <th>Tank Lorry</th>
                        <th class="text-end">Sold Stock</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">Amount</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Get details table data
    $('#detailsTable tbody tr').each(function(index) {
        if (!$(this).hasClass('table-light')) {
            content += '<tr>';
            $(this).find('td').each(function() {
                const cellText = $(this).text().trim();
                const cellClass = $(this).hasClass('text-center') ? ' class="text-center"' :
                                 $(this).hasClass('text-end') ? ' class="text-end"' : '';
                content += `<td${cellClass}>${cellText}</td>`;
            });
            content += '</tr>';
        } else {
            content += '<tr class="total-row">';
            $(this).find('td').each(function() {
                const cellText = $(this).text().trim();
                const cellClass = $(this).hasClass('text-center') ? ' class="text-center"' :
                                 $(this).hasClass('text-end') ? ' class="text-end"' : '';
                content += `<td${cellClass}>${cellText}</td>`;
            });
            content += '</tr>';
        }
    });

    content += `
                </tbody>
            </table>
        </div>
        </body>
        </html>
    `;

    return content;
}

/**
 * Get current date in YYYY-MM-DD format
 */
function getCurrentDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Format number with commas
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * Show loading spinner
 */
function showLoading() {
    $('body').addClass('loading');
}

/**
 * Hide loading spinner
 */
function hideLoading() {
    $('body').removeClass('loading');
}

/**
 * Show success message
 */
function showSuccess(message) {
    // You can integrate with your notification system here
    console.log('Success:', message);
}

/**
 * Show error message
 */
function showError(message) {
    // You can integrate with your notification system here
    console.error('Error:', message);
}

/**
 * Select option function (matching old project)
 */
function selectOption(selectId, vendorId, vendorType) {
    const selectElement = $('#' + selectId);
    const option = selectElement.find(`option[value="${vendorId}"][data-type="${vendorType}"]`);
    if (option.length) {
        option.prop('selected', true);
        selectElement.trigger('change');
    }
}

/**
 * Debounce function to limit API calls
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
