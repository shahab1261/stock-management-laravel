$(document).ready(function () {
    document.title = 'Credit Sales History';

    // Ensure DataTables matches other history tabs
    if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#credit-sales-history-table')) {
        // $('#credit-sales-history-table').addClass('history-table');
        $("#credit-sales-history-table").each(function () {
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
    }
});

