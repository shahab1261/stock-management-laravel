/**
 * Journal Voucher JavaScript
 * Handles dynamic form creation and validation for journal entries
 */

$(document).ready(function () {
    let partyCounter = 1;

    // Initialize
    initializeJournalForm();
    
    // Auto-initialize with two entry rows on page load (for inline form)
    addInitialPartyRows();

    /**
     * Initialize journal form functionality
     */
    function initializeJournalForm() {
        // Add party button
        $("#add_party_btn").click(function () {
            const btn = $(this);
            btn.prop('disabled', true);
            btn.html('<i class="bi bi-hourglass-split me-1"></i> Adding...');

            addPartyRow(function() {
                btn.prop('disabled', false);
                btn.html('<i class="bi bi-plus-circle me-1"></i> Add Entry');
            });
        });

        // Form submission
        $("#journalForm").submit(function (e) {
            e.preventDefault();
            submitJournalEntries();
        });

        // Delete journal entry - open confirmation modal
        $(document).on("click", ".delete-btn", function () {
            const id = $(this).data("id");
            $("#delete_entry_id").val(id);

            // Load voucher details
            loadVoucherDetails(id);
            $("#deleteModal").modal("show");
        });

        // Amount input validation
        $(document).on("input", ".amount-input", function () {
            validateAmounts();
        });

        // Vendor selection change
        $(document).on("change", ".vendor-dropdown", function () {
            updateDescription();
        });

        // Reset form button
        $("#resetFormBtn").click(function () {
            resetForm();
            addInitialPartyRows();
        });
    }

    /**
     * Reset form to initial state
     */
    function resetForm() {
        // Destroy all Select2 instances before clearing
        $(".vendor-dropdown").each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        $("#append_parties").empty();
        partyCounter = 1;
        $("#debit_sum_div").text("Rs 0.00");
        $("#credit_sum_div").text("Rs 0.00");
        $("#balance_amount").text("Rs 0.00");
        $("#journal_description").val("");
        $("#transaction_btn").prop("disabled", true);
    }

    /**
     * Add initial party rows
     */
    function addInitialPartyRows() {
        addPartyRow(function() {
            // Add second row after first is initialized
            setTimeout(function() {
                addPartyRow();
            }, 100);
        });
    }

    /**
     * Add a new party row
     */
    function addPartyRow(callback) {
        const rowHtml = `
            <div class="row align-items-end party-row mb-3" id="row_${partyCounter}">
                <div class="col-12 mb-2">
                    <span class="badge bg-secondary">Entry #${partyCounter}</span>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Select Account <span class="text-danger">*</span></label>
                    <select class="form-select vendor-dropdown" id="journal_vendor_${partyCounter}" data-row="${partyCounter}" required>
                        <option value="">Select Account</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Debit Amount</label>
                    <input type="number" step="0.01" class="form-control amount-input debit-amount"
                           id="debit_amount_${partyCounter}" value="0" min="0" data-row="${partyCounter}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Credit Amount</label>
                    <input type="number" step="0.01" class="form-control amount-input credit-amount"
                           id="credit_amount_${partyCounter}" value="0" min="0" data-row="${partyCounter}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="deletePartyRow(${partyCounter})" title="Remove Entry">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;

        $("#append_parties").append(rowHtml);

        // Store current counter before incrementing
        const currentCounter = partyCounter;

        // Populate all vendors in the dropdown
        populateAllVendors(currentCounter);

        // Use setTimeout to ensure DOM is ready before initializing Select2
        setTimeout(function() {
            try {
                // Initialize Select2 for this dropdown (no modal parent needed for inline form)
                $(`#journal_vendor_${currentCounter}`).select2({
                    placeholder: 'Search and select account',
                    allowClear: true,
                    width: '100%',
                    theme: 'default'
                });

                // Call callback if provided
                if (typeof callback === 'function') {
                    callback();
                }
            } catch (error) {
                console.error('Error initializing Select2:', error);
                // Call callback anyway to re-enable button
                if (typeof callback === 'function') {
                    callback();
                }
            }
        }, 50);

        // Increment counter for next row
        partyCounter++;
    }

    /**
     * Populate all vendors in a single dropdown
     */
    function populateAllVendors(row) {
        const vendorSelect = $(`#journal_vendor_${row}`);
        vendorSelect.empty().append('<option value="">Select Account</option>');

        // Add Suppliers
        if (window.vendorData.suppliers && window.vendorData.suppliers.length > 0) {
            window.vendorData.suppliers.forEach((vendor) => {
                const name = vendor.vendor_name || vendor.name;
                vendorSelect.append(
                    `<option value="${vendor.id}" data-vendor="${vendor.id}" data-vendor-type="1">${name} (Supplier)</option>`
                );
            });
        }

        // Add Customers
        if (window.vendorData.customers && window.vendorData.customers.length > 0) {
            window.vendorData.customers.forEach((vendor) => {
                const name = vendor.vendor_name || vendor.name;
                vendorSelect.append(
                    `<option value="${vendor.id}" data-vendor="${vendor.id}" data-vendor-type="2">${name} (Customer)</option>`
                );
            });
        }

        // Add Products
        if (window.vendorData.products && window.vendorData.products.length > 0) {
            window.vendorData.products.forEach((product) => {
                vendorSelect.append(
                    `<option value="${product.id}" data-vendor="${product.id}" data-vendor-type="3">${product.name} (Product)</option>`
                );
            });
        }

        // Add Expenses
        if (window.vendorData.expenses && window.vendorData.expenses.length > 0) {
            window.vendorData.expenses.forEach((expense) => {
                vendorSelect.append(
                    `<option value="${expense.id}" data-vendor="${expense.id}" data-vendor-type="4">${expense.expense_name} (Expense)</option>`
                );
            });
        }

        // Add Incomes
        if (window.vendorData.incomes && window.vendorData.incomes.length > 0) {
            window.vendorData.incomes.forEach((income) => {
                vendorSelect.append(
                    `<option value="${income.id}" data-vendor="${income.id}" data-vendor-type="5">${income.income_name} (Income)</option>`
                );
            });
        }

        // Add Banks
        if (window.vendorData.banks && window.vendorData.banks.length > 0) {
            window.vendorData.banks.forEach((bank) => {
                const name = bank.vendor_name || bank.name;
                vendorSelect.append(
                    `<option value="${bank.id}" data-vendor="${bank.id}" data-vendor-type="6">${name} (Bank)</option>`
                );
            });
        }

        // Add Cash
        vendorSelect.append(
            `<option value="7" data-vendor="7" data-vendor-type="7">Cash (Cash)</option>`
        );

        // Add MP
        vendorSelect.append(
            `<option value="8" data-vendor="8" data-vendor-type="8">MP (MP)</option>`
        );
    }

    /**
     * Delete party row
     */
    window.deletePartyRow = function (id) {
        // Destroy Select2 instance before removing
        if ($(`#journal_vendor_${id}`).hasClass('select2-hidden-accessible')) {
            $(`#journal_vendor_${id}`).select2('destroy');
        }
        $(`#row_${id}`).remove();
        validateAmounts();
    };

    /**
     * Validate amounts and update totals
     */
    function validateAmounts() {
        let debitSum = 0;
        let creditSum = 0;
        let hasError = false;

        $(".party-row").each(function () {
            const debitAmount =
                parseFloat($(this).find(".debit-amount").val()) || 0;
            const creditAmount =
                parseFloat($(this).find(".credit-amount").val()) || 0;

            // Check if both debit and credit have values
            if (debitAmount > 0 && creditAmount > 0) {
                hasError = true;
                showAlert("error", "One amount must be 0 in each row");
                return false;
            }

            // Check if vendor is selected when amount is entered
            const vendorId = $(this).find(".vendor-dropdown").val();
            if ((debitAmount > 0 || creditAmount > 0) && !vendorId) {
                hasError = true;
                showAlert(
                    "error",
                    "Please select an account for each entry with amount"
                );
                return false;
            }

            debitSum += debitAmount;
            creditSum += creditAmount;
        });

        // Update totals
        $("#debit_sum_div").text(`Rs ${debitSum.toFixed(2)}`);
        $("#credit_sum_div").text(`Rs ${creditSum.toFixed(2)}`);

        const balance = Math.abs(debitSum - creditSum);
        $("#balance_amount").text(`Rs ${balance.toFixed(2)}`);

        // Update balance alert
        const balanceAlert = $("#balance_alert");
        if (balance === 0 && debitSum > 0 && creditSum > 0) {
            balanceAlert
                .removeClass("alert-info alert-warning")
                .addClass("alert-success");
            balanceAlert
                .find("small")
                .html(
                    '<i class="bi bi-check-circle me-1"></i>Balanced: <span id="balance_amount">Rs 0.00</span>'
                );
        } else if (balance > 0) {
            balanceAlert
                .removeClass("alert-info alert-success")
                .addClass("alert-warning");
            balanceAlert
                .find("small")
                .html(
                    '<i class="bi bi-exclamation-triangle me-1"></i>Unbalanced: <span id="balance_amount">Rs ' +
                        balance.toFixed(2) +
                        "</span>"
                );
        } else {
            balanceAlert
                .removeClass("alert-success alert-warning")
                .addClass("alert-info");
            balanceAlert
                .find("small")
                .html(
                    '<i class="bi bi-info-circle me-1"></i>Balance: <span id="balance_amount">Rs 0.00</span>'
                );
        }

        // Enable/disable submit button
        const canSubmit = !hasError && debitSum === creditSum && debitSum > 0;
        $("#transaction_btn").prop("disabled", !canSubmit);

        updateDescription();
    }

    /**
     * Update description automatically
     */
    function updateDescription() {
        let description = "";
        let fromCounter = 0;
        let toCounter = 0;

        $(".party-row").each(function () {
            const debitAmount =
                parseFloat($(this).find(".debit-amount").val()) || 0;
            const creditAmount =
                parseFloat($(this).find(".credit-amount").val()) || 0;
            const vendorSelect = $(this).find(".vendor-dropdown");

            if (vendorSelect[0].selectedIndex > 0) {
                const selectedOption =
                    vendorSelect[0].options[vendorSelect[0].selectedIndex];
                const vendorName = selectedOption.text.split(' (')[0]; // Get name before the type

                if (debitAmount > 0) {
                    fromCounter++;
                    if (fromCounter === 1) {
                        description += "To " + vendorName + " ";
                    } else {
                        description += ", " + vendorName + " ";
                    }
                } else if (creditAmount > 0) {
                    toCounter++;
                    if (toCounter === 1) {
                        description += "From " + vendorName + " ";
                    } else {
                        description += ", " + vendorName + " ";
                    }
                }
            }
        });

        $("#journal_description").val(description.trim());
    }

    /**
     * Submit journal entries
     */
    function submitJournalEntries() {
        const submitBtn = $("#transaction_btn");
        submitBtn
            .prop("disabled", true)
            .html('<i class="bi bi-hourglass-split me-1"></i> Please wait...');

        const entries = [];
        let hasError = false;
        const description = $("#journal_description").val();

        $(".party-row").each(function () {
            const debitAmount =
                parseFloat($(this).find(".debit-amount").val()) || 0;
            const creditAmount =
                parseFloat($(this).find(".credit-amount").val()) || 0;
            const vendorSelect = $(this).find(".vendor-dropdown");

            if (debitAmount > 0 || creditAmount > 0) {
                if (!vendorSelect.val()) {
                    hasError = true;
                    return false;
                }

                const selectedOption =
                    vendorSelect[0].options[vendorSelect[0].selectedIndex];

                entries.push({
                    vendor_id_from: selectedOption.getAttribute("data-vendor"),
                    vendor_from_type: selectedOption.getAttribute("data-vendor-type"),
                    journal_amount:
                        debitAmount > 0 ? debitAmount : creditAmount,
                    journal_description: description,
                    journal_date: $("#journal_date").val(),
                    debit_credit: debitAmount > 0 ? "2" : "1", // 2=debit, 1=credit
                    voucher_id: null, // Will be set after first entry gets voucher_id
                });
            }
        });

        if (hasError || entries.length === 0) {
            showAlert("error", "Please complete all entries properly");
            submitBtn
                .prop("disabled", false)
                .html(
                    '<i class="bi bi-check-circle me-1"></i> Submit Journal Entry'
                );
            return;
        }

        // Submit each entry
        submitEntries(entries, 0, submitBtn);
    }

    /**
     * Submit entries recursively
     */
    function submitEntries(entries, index, submitBtn) {
        if (index >= entries.length) {
            // All entries submitted successfully
            showAlert("success", "Journal entries saved successfully!");
            setTimeout(() => {
                location.reload();
            }, 1500);
            return;
        }

        const entry = entries[index];

        $.ajax({
            url: window.routes.store,
            type: "POST",
            data: {
                ...entry,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.status === "success") {
                    // Store voucher_id from first entry for subsequent entries
                    if (index === 0 && response.voucher_id) {
                        currentVoucherId = response.voucher_id;
                        // Update all remaining entries with the same voucher_id
                        for (let i = index + 1; i < entries.length; i++) {
                            entries[i].voucher_id = response.voucher_id;
                        }
                    }

                    // Submit next entry
                    submitEntries(entries, index + 1, submitBtn);
                } else {
                    showAlert(
                        "error",
                        response.message || "Failed to save entry"
                    );
                    submitBtn
                        .prop("disabled", false)
                        .html(
                            '<i class="bi bi-check-circle me-1"></i> Submit Journal Entry'
                        );
                }
            },
            error: function (xhr) {
                const response = xhr.responseJSON;
                showAlert("error", response?.message || "Failed to save entry");
                submitBtn
                    .prop("disabled", false)
                    .html(
                        '<i class="bi bi-check-circle me-1"></i> Submit Journal Entry'
                    );
            },
        });
    }

    // Confirm delete - perform AJAX delete
    $(document).on("click", "#confirmDeleteBtn", function () {
        const id = $("#delete_entry_id").val();

        // Button loading state
        const btn = $("#confirmDeleteBtn");
        btn.attr("disabled", true);
        btn.find(".spinner-border").removeClass("d-none");

        $.ajax({
            url: window.routes.delete.replace(":id", id),
            type: "DELETE",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.status === "success") {
                    $("#deleteModal").modal("hide");
                    showAlert(
                        "success",
                        response.message || "Deleted successfully"
                    );
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert("error", response.message || "Please try again");
                }
            },
            error: function (xhr) {
                const response = xhr.responseJSON;
                showAlert(
                    "error",
                    response?.message || "An error occurred. Please try again."
                );
            },
            complete: function () {
                btn.attr("disabled", false);
                btn.find(".spinner-border").addClass("d-none");
            },
        });
    });

    /**
     * Load voucher details for delete confirmation
     */
    function loadVoucherDetails(entryId) {
        $.ajax({
            url: window.routes.getVoucherDetails.replace(":id", entryId),
            type: "GET",
            success: function (response) {
                if (response.status === "success") {
                    $("#voucher-id-display").text(response.voucher_id);
                    $("#total-entries-display").text(response.total_entries);

                    // Build entries list
                    let entriesHtml = '<div class="list-group">';
                    response.entries.forEach(function (entry) {
                        const type =
                            entry.debit_credit == 2 ? "Debit" : "Credit";
                        const amount = parseFloat(entry.amount).toFixed(2);
                        entriesHtml += `
                            <div class="list-group-item py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${entry.vendor_name}</strong>
                                        <br><small class="text-muted">${
                                            entry.description
                                        }</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge ${
                                            entry.debit_credit == 2
                                                ? "bg-success"
                                                : "bg-danger"
                                        }">${type}</span>
                                        <br><strong>Rs ${amount}</strong>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    entriesHtml += "</div>";

                    $("#entries-list").html(entriesHtml);
                    $("#voucher-details").show();
                } else {
                    $("#voucher-details").hide();
                }
            },
            error: function () {
                $("#voucher-details").hide();
            },
        });
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        const icon =
            type === "success"
                ? "success"
                : type === "error"
                ? "error"
                : "info";

        Swal.fire({
            title:
                type === "success"
                    ? "Success!"
                    : type === "error"
                    ? "Error!"
                    : "Info",
            text: message,
            icon: icon,
            confirmButtonColor: "#4154f1",
            confirmButtonText: "OK",
        });
    }
});
