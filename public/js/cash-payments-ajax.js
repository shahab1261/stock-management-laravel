$(document).ready(function() {
    // Open Add Payment Modal
    $('#addNewPaymentBtn').click(function() {
        $('#addPaymentModal').modal('show');
        $('#cashPaymentForm')[0].reset();
        $('#journal_vendor').val('');
        clearErrors();
    });

    // Cash Payment Form Submission
    $("#cashPaymentForm").submit(function(e) {
        e.preventDefault();

        $("#saveBtn").attr("disabled", true);
        $("#saveBtn .spinner-border").removeClass("d-none");
        $("#saveBtnText").text("Please wait...");

        var vendorDropdown = $("#journal_vendor").find('option:selected');
        var vendorId = $("#journal_vendor").val();
        var vendorType = vendorDropdown.data('type');
        var vendorName = vendorDropdown.data('name');

        var transactionAmount = $("#transaction_amount").val();
        var transactionDate = $("#transaction_date").val();
        var transactionDescription = $("#transaction_description").val();

        var isValidNumber = isNumber(transactionAmount);
        if (!isValidNumber) {
            showError("Invalid Amount");
            resetButton();
            return;
        }

        // AJAX request
        $.ajax({
            url: $("#cashPaymentForm").attr('action'),
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                vendor_id: vendorId,
                vendor_type: vendorType,
                vendor_name: vendorName,
                transaction_amount: transactionAmount,
                transaction_date: transactionDate,
                transaction_description: transactionDescription
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message, function() {
                        location.reload();
                    });
                } else {
                    showError(response.message || "Please try again");
                }
                resetButton();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    displayValidationErrors(errors);
                } else {
                    showError("An error occurred. Please try again.");
                }
                resetButton();
            }
        });
    });

    // Delete Transaction
    $(document).on('click', '.delete-btn', function() {
        var transactionId = $(this).data('id');
        var ledgerType = $(this).data('ledger-type');

        $('#delete_transaction_id').val(transactionId);
        $('#delete_ledger_type').val(ledgerType);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').click(function() {
        var transactionId = $('#delete_transaction_id').val();
        var ledgerType = $('#delete_ledger_type').val();

        $("#confirmDeleteBtn").attr("disabled", true);
        $("#confirmDeleteBtn .spinner-border").removeClass("d-none");

        $.ajax({
            url: '/payments/transaction/delete',
            type: "DELETE",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                transaction_id: transactionId,
                ledger_purchase_type: ledgerType
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message, function() {
                        location.reload();
                    });
                } else {
                    showError(response.message || "Please try again");
                }
            },
            error: function() {
                showError("An error occurred. Please try again.");
            },
            complete: function() {
                $("#confirmDeleteBtn").attr("disabled", false);
                $("#confirmDeleteBtn .spinner-border").addClass("d-none");
                $('#deleteModal').modal('hide');
            }
        });
    });

    // Helper Functions
    function isNumber(value) {
        return !isNaN(value) && !isNaN(parseFloat(value)) && parseFloat(value) >= 0;
    }

    function resetButton() {
        $("#saveBtn").attr("disabled", false);
        $("#saveBtn .spinner-border").addClass("d-none");
        $("#saveBtnText").text("Save");
    }

    function clearErrors() {
        $('.invalid-feedback').text('');
        $('.form-control, .form-select').removeClass('is-invalid');
    }

    function displayValidationErrors(errors) {
        clearErrors();
        $.each(errors, function(field, messages) {
            var fieldElement = $('#' + field);
            fieldElement.addClass('is-invalid');
            $('#' + field + '-error').text(messages[0]);
        });
    }

    function showSuccess(message, callback) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#4154f1'
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    }

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    }
});
