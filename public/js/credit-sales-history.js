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

