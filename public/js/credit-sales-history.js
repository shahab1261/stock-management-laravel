$(document).ready(function () {
    document.title = 'Credit Sales History';

    // Ensure DataTables matches other history tabs
    if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#credit-sales-history-table')) {
        // $('#credit-sales-history-table').addClass('history-table');
        $("#credit-sales-history-table").each(function () {
            if (!$.fn.DataTable.isDataTable(this)) {
                var $table = $(this);
                // Find the buttons container in the card header
                var $buttonsContainer = $table.closest('.card').find('.card-header .dt-buttons-container');

                var table = $(this).DataTable({
                    processing: true,
                    responsive: false,
                    scrollX: true,
                    searching: true,
                    // Custom DOM: show entries left with margin, search box right, buttons (will be moved to header)
                    dom: '<"row align-items-center"<"col-md-6 dt-left-margin"l><"col-md-6 d-flex justify-content-end"fB>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
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
                            text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                            className: 'btn btn-primary btn-sm ms-2',
                            exportOptions: {
                                format: {
                                    body: function (data, row, column, node) {
                                        // Extract text content from node (strips HTML tags)
                                        var text = '';
                                        if (node) {
                                            text = $(node).text().trim();
                                        } else if (data) {
                                            // If no node, create a temporary element to extract text
                                            text = $('<div>').html(data).text().trim();
                                        }

                                        // Remove Rs, rs, LTR, ltr from the exported data
                                        if (text) {
                                            text = text
                                                .replace(/Rs\s*/gi, '')  // Remove Rs or rs (case insensitive)
                                                .replace(/\s*ltr\s*/gi, '')  // Remove ltr or LTR (case insensitive) with spaces
                                                .trim();
                                        }
                                        return text || '';
                                    }
                                }
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                            className: 'btn btn-success btn-sm ms-2',
                            exportOptions: {
                                format: {
                                    body: function (data, row, column, node) {
                                        // Extract text content from node (strips HTML tags)
                                        var text = '';
                                        if (node) {
                                            text = $(node).text().trim();
                                        } else if (data) {
                                            // If no node, create a temporary element to extract text
                                            text = $('<div>').html(data).text().trim();
                                        }

                                        // Remove Rs, rs, LTR, ltr from the exported data
                                        if (text) {
                                            text = text
                                                .replace(/Rs\s*/gi, '')  // Remove Rs or rs (case insensitive)
                                                .replace(/\s*ltr\s*/gi, '')  // Remove ltr or LTR (case insensitive) with spaces
                                                .trim();
                                        }
                                        return text || '';
                                    }
                                }
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                            className: 'btn btn-danger btn-sm ms-2',
                            exportOptions: {
                                format: {
                                    body: function (data, row, column, node) {
                                        // Extract text content from node (strips HTML tags)
                                        var text = '';
                                        if (node) {
                                            text = $(node).text().trim();
                                        } else if (data) {
                                            // If no node, create a temporary element to extract text
                                            text = $('<div>').html(data).text().trim();
                                        }

                                        // Remove Rs, rs, LTR, ltr from the exported data
                                        if (text) {
                                            text = text
                                                .replace(/Rs\s*/gi, '')  // Remove Rs or rs (case insensitive)
                                                .replace(/\s*ltr\s*/gi, '')  // Remove ltr or LTR (case insensitive) with spaces
                                                .trim();
                                        }
                                        return text || '';
                                    }
                                }
                            }
                        }
                    ],
                    drawCallback: function () {
                        // Reinitialize tooltips after table redraw
                        $('[data-bs-toggle="tooltip"]').tooltip("dispose");
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    },
                });

                // Move buttons to header container after initialization
                // Use setTimeout to ensure buttons are rendered first
                setTimeout(function() {
                    if ($buttonsContainer.length) {
                        var $buttonsWrapper = $table.closest('.card').find('.dt-buttons');
                        if ($buttonsWrapper.length) {
                            // Simply move buttons to header - no need to hide anything
                            // The buttons will be removed from their original location automatically
                            $buttonsWrapper.appendTo($buttonsContainer);
                        } else {
                            // If buttons wrapper not found, try to get from DataTable API
                            var buttons = table.buttons();
                            if (buttons && buttons.container) {
                                var $btnContainer = $(buttons.container());
                                $btnContainer.appendTo($buttonsContainer);
                            }
                        }
                    }
                }, 100);

                // Add left margin to the show entries dropdown
                $('.dt-left-margin').css('padding-left', '15px');
            }
        });
    }
});

// Delete credit sale from history table (same route/handler as main tab)
$(document).on('click', '.delete-credit-sale-btn', function() {
    var tid = $(this).data("id");
    var ledgerpurchasetype = $(this).data("ledgerpurchasetype");

    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to delete this credit sale?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/sales/credit/delete',
                type: "POST",
                data: {
                    tid: tid,
                    ledgerpurchasetype: ledgerpurchasetype,
                    "_token": $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Credit sale has been deleted successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Please try again!'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete credit sale!'
                    });
                }
            });
        }
    });
});

