$(document).ready(function () {
    $(".account-history-table").each(function () {
        // $(this).find('tfoot').hide();
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                processing: true,
                responsive: false,
                scrollX: true,
                searching: true,
                dom: '<"row align-items-center"<"col-md-6 dt-left-margin"l><"col-md-6 d-flex justify-content-end"fB>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100],
                ],
                pageLength: 10,
                order: [[0, "desc"]],
                buttons: [
                    {
                        extend: "csvHtml5",
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> Export to CSV',
                        className: "btn btn-primary btn-sm mb-2 ms-2",
                    },
                ],
                drawCallback: function () {
                    $('[data-bs-toggle="tooltip"]').tooltip("dispose");
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
            });

            $(".dt-left-margin").css("padding-left", "15px");
        }
    });

    $('[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        setTimeout(function () {
            $.fn.dataTable
                .tables({ visible: true, api: true })
                .columns.adjust();
        }, 100);
    });
    initializeAccountHistoryPage();

    /**
     * Initialize account history page functionality
     */
    function initializeAccountHistoryPage() {

        // Handle vendor dropdown change
        handleVendorDropdownChange();

        // Handle form submission
        handleFormSubmission();

        // Date validation
        handleDateValidation();

        // Initialize tooltips
        initializeTooltips();

        // Print functionality
        initializePrintFunctionality();
    }

    /**
     * Handle vendor dropdown change
     */
    function handleVendorDropdownChange() {
        $("#vendor_dropdown").on("change", function () {
            const selectedOption = $(this).find("option:selected");
            const vendorId = $(this).val();
            const vendorType = selectedOption.data("type");
            const vendorName = selectedOption.data("name");

            // Update hidden fields
            $("#vendor_id").val(vendorId);
            $("#vendor_type").val(vendorType);
            $("#vendor_name").val(vendorName);
        });
    }

    /**
     * Handle form submission
     */
    function handleFormSubmission() {
        $("form").on("submit", function (e) {
            const startDate = $("#start_date").val();
            const endDate = $("#end_date").val();
            const vendorDropdown = $("#vendor_dropdown").val();

            // Validate vendor selection
            if (!vendorDropdown) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Account Required",
                    text: "Please select an account to view history.",
                });
                return false;
            }

            // Validate date range
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

            // Show loading state
            showLoading();
        });
    }

    /**
     * Handle date validation
     */
    function handleDateValidation() {
        $("#start_date, #end_date").on("change", function () {
            const startDate = $("#start_date").val();
            const endDate = $("#end_date").val();

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
    }

    /**
     * Initialize tooltips
     */
    function initializeTooltips() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    /**
     * Initialize print functionality
     */
    function initializePrintFunctionality() {
        // Add print button to page header if needed
        if ($(".account-history-table").length > 0) {
            // Print functionality is handled by DataTables buttons
        }
    }

    /**
     * Show loading spinner
     */
    function showLoading() {
        Swal.fire({
            title: "Loading Account History...",
            text: "Please wait while we fetch the transaction data",
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
     * Format numbers for display
     */
    function formatNumber(number) {
        return new Intl.NumberFormat("en-US").format(number);
    }

    /**
     * Format currency for display
     */
    function formatCurrency(amount) {
        return "Rs " + formatNumber(amount);
    }

    /**
     * Get date range display text
     */
    function getDateRangeText() {
        const startDate = $("#start_date").val();
        const endDate = $("#end_date").val();

        if (startDate && endDate) {
            const start = new Date(startDate).toLocaleDateString();
            const end = new Date(endDate).toLocaleDateString();
            return `${start} to ${end}`;
        }

        return "Current Month";
    }

    /**
     * Export data functionality
     */
    function exportData(format) {
        const vendorName = $("#vendor_name").val();
        const dateRange = getDateRangeText();
        const filename = `Account_History_${vendorName}_${dateRange}`.replace(
            /[^a-z0-9]/gi,
            "_"
        );

        // This would be handled by DataTables export buttons
        console.log(`Exporting ${format} for ${filename}`);
    }

    /**
     * Responsive table handling for mobile
     */
    $(window).on("resize", function () {
        setTimeout(function () {
            $.fn.dataTable
                .tables({ visible: true, api: true })
                .columns.adjust();
        }, 100);
    });

    /**
     * Handle scroll to top functionality
     */
    function addScrollToTop() {
        // Add scroll to top button
        $("body").append(`
            <button id="scrollToTop" class="btn btn-primary position-fixed"
                    style="bottom: 20px; right: 20px; z-index: 1000; display: none;">
                <i class="bi bi-arrow-up"></i>
            </button>
        `);

        $(window).scroll(function () {
            if ($(this).scrollTop() > 300) {
                $("#scrollToTop").fadeIn();
            } else {
                $("#scrollToTop").fadeOut();
            }
        });

        $("#scrollToTop").click(function () {
            $("html, body").animate({ scrollTop: 0 }, 300);
            return false;
        });
    }

    // Initialize scroll to top
    addScrollToTop();

    /**
     * Handle table row highlighting
     */
    $(".account-history-table tbody")
        .on("mouseenter", "tr", function () {
            $(this).addClass("table-hover-highlight");
        })
        .on("mouseleave", "tr", function () {
            $(this).removeClass("table-hover-highlight");
        });

    /**
     * Handle print individual sections
     */
    function printSection(sectionId) {
        const printContent = document.getElementById(sectionId);
        const printWindow = window.open("", "_blank");

        printWindow.document.write(`
            <html>
            <head>
                <title>Account History - ${$("#vendor_name").val()}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @media print {
                        .table { font-size: 10px; }
                        .table th, .table td { padding: 4px; border: 1px solid #000; }
                        .table th { background-color: #f0f0f0; font-weight: bold; }
                    }
                </style>
            </head>
            <body>
                <div class="container-fluid">
                    <h3 class="text-center mb-4">Account History - ${$(
                        "#vendor_name"
                    ).val()}</h3>
                    <p class="text-center mb-4">${getDateRangeText()}</p>
                    ${printContent.outerHTML}
                </div>
            </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();

        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }

    // Expose print function globally
    window.printAccountHistorySection = printSection;

    /**
     * Handle keyboard shortcuts
     */
    $(document).keydown(function (e) {
        // Ctrl+P for print
        if (e.ctrlKey && e.keyCode === 80) {
            e.preventDefault();
            window.print();
        }

        // Ctrl+F for search (focus on first DataTable search)
        if (e.ctrlKey && e.keyCode === 70) {
            e.preventDefault();
            $(".dataTables_filter input").first().focus();
        }

        // Escape to clear search
        if (e.keyCode === 27) {
            $(".dataTables_filter input").val("").trigger("keyup");
        }
    });

    /**
     * Add summary statistics
     */
    function calculateSummaryStats() {
        const stats = {
            totalPurchases: 0,
            totalSales: 0,
            totalCashReceipts: 0,
            totalCashPayments: 0,
            totalBankReceivings: 0,
            totalBankPayments: 0,
        };

        // Calculate from visible table data
        $(".account-history-table tfoot .fw-bold").each(function () {
            // Extract amounts from total rows
            // This would need to be implemented based on specific table structure
        });

        return stats;
    }

    // Initialize summary if needed
    const summaryStats = calculateSummaryStats();
    console.log("Account History Summary:", summaryStats);

    // Hide loading when page is fully loaded
    $(window).on("load", function () {
        hideLoading();
    });
});
