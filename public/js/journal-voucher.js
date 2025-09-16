/**
 * Journal Voucher JavaScript
 * Handles dynamic form creation and validation for journal entries
 */

$(document).ready(function() {
    let partyCounter = 1;
    let currentVoucherId = null;

    // Initialize
    initializeJournalForm();

    /**
     * Initialize journal form functionality
     */
    function initializeJournalForm() {
        // Add new journal entry button
        $('#addJournalBtn').click(function() {
            $('#journalModal').modal('show');
            resetForm();
            addInitialPartyRows();
            currentVoucherId = null; // Reset voucher ID for new entry
        });

        // Add party button
        $('#add_party_btn').click(function() {
            addPartyRow();
        });

        // Form submission
        $('#journalForm').submit(function(e) {
            e.preventDefault();
            submitJournalEntries();
        });

        // Delete journal entry - open confirmation modal
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            const voucherId = $(this).data('voucher-id');
            $('#delete_entry_id').val(id);

            // Load voucher details
            loadVoucherDetails(id);
            $('#deleteModal').modal('show');
        });

        // Amount input validation
        $(document).on('input', '.amount-input', function() {
            validateAmounts();
        });

        // Vendor selection change
        $(document).on('change', '.vendor-dropdown', function() {
            updateDescription();
        });
    }

    /**
     * Reset form to initial state
     */
    function resetForm() {
        $('#append_parties').empty();
        partyCounter = 1;
        $('#debit_sum_div').text('Rs 0.00');
        $('#credit_sum_div').text('Rs 0.00');
        $('#balance_amount').text('Rs 0.00');
        $('#transaction_btn').prop('disabled', true);
    }

    /**
     * Add initial party rows
     */
    function addInitialPartyRows() {
        addPartyRow();
        addPartyRow();
    }

    /**
     * Add a new party row
     */
    function addPartyRow() {
        const rowHtml = `
            <div class="row align-items-end party-row mb-3" id="row_${partyCounter}">
                <div class="col-md-3">
                    <label class="form-label">Account Type</label>
                    <select class="form-select account-type-dropdown" id="account_type_${partyCounter}" data-row="${partyCounter}">
                        <option value="">Select Type</option>
                        <option value="1">Supplier</option>
                        <option value="2">Customer</option>
                        <option value="3">Product</option>
                        <option value="4">Expense</option>
                        <option value="5">Income</option>
                        <option value="6">Bank</option>
                        <option value="7">Cash</option>
                        <option value="8">MP</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Select Account</label>
                    <select class="form-select vendor-dropdown" id="journal_vendor_${partyCounter}" data-row="${partyCounter}" required disabled>
                        <option value="">Select Account</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Debit Amount</label>
                    <input type="number" step="0.01" class="form-control amount-input debit-amount"
                           id="debit_amount_${partyCounter}" value="0" min="0" data-row="${partyCounter}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Credit Amount</label>
                    <input type="number" step="0.01" class="form-control amount-input credit-amount"
                           id="credit_amount_${partyCounter}" value="0" min="0" data-row="${partyCounter}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="deletePartyRow(${partyCounter})" title="Remove Entry">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="col-12 mt-2">
                    <label class="form-label">Description</label>
                    <textarea class="form-control description" rows="1" id="journal_description_${partyCounter}"
                              placeholder="Transaction description will be auto-generated" readonly></textarea>
                </div>
            </div>
        `;

        $('#append_parties').append(rowHtml);

        // Add account type change handler
        $(`#account_type_${partyCounter}`).change(function() {
            const row = $(this).data('row');
            const accountType = $(this).val();
            loadVendorsByType(accountType, row);
        });

        partyCounter++;
    }

    /**
     * Load vendors by account type
     */
    function loadVendorsByType(accountType, row) {
        const vendorSelect = $(`#journal_vendor_${row}`);
        vendorSelect.empty().append('<option value="">Select Account</option>');

        if (!accountType) {
            vendorSelect.prop('disabled', true);
            return;
        }

        let vendors = [];

        switch (accountType) {
            case '1': // Suppliers
                vendors = window.vendorData.suppliers;
                break;
            case '2': // Customers
                vendors = window.vendorData.customers;
                break;
            case '3': // Products
                vendors = window.vendorData.products.map(p => ({
                    id: p.id,
                    vendor_name: p.name,
                    name: p.name,
                    vendor_type: 3
                }));
                break;
            case '4': // Expenses
                vendors = window.vendorData.expenses.map(e => ({
                    id: e.id,
                    // vendor_name: e.name,
                    name: e.expense_name,
                    vendor_type: 4
                }));
                break;
            case '5': // Incomes
                vendors = window.vendorData.incomes.map(i => ({
                    id: i.id,
                    // vendor_name: i.name,
                    name: i.income_name,
                    vendor_type: 5
                }));
                break;
            case '6': // Banks
                vendors = window.vendorData.banks;
                break;
            case '7': // Cash
                vendors = [{id: 1, vendor_name: 'Cash', name: 'Cash', vendor_type: 7}];
                break;
            case '8': // MP
                vendors = [{id: 1, vendor_name: 'MP', name: 'MP', vendor_type: 8}];
                break;
        }

        vendors.forEach(vendor => {
            const name = vendor.vendor_name || vendor.name;
            vendorSelect.append(`<option value="${vendor.id}" data-type="${accountType}" data-name="${name}">${name}</option>`);
        });

        vendorSelect.prop('disabled', false);
    }

    /**
     * Delete party row
     */
    window.deletePartyRow = function(id) {
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

        $('.party-row').each(function() {
            const debitAmount = parseFloat($(this).find('.debit-amount').val()) || 0;
            const creditAmount = parseFloat($(this).find('.credit-amount').val()) || 0;

            // Check if both debit and credit have values
            if (debitAmount > 0 && creditAmount > 0) {
                hasError = true;
                showAlert('error', 'One amount must be 0 in each row');
                return false;
            }

            // Check if vendor is selected when amount is entered
            const vendorId = $(this).find('.vendor-dropdown').val();
            if ((debitAmount > 0 || creditAmount > 0) && !vendorId) {
                hasError = true;
                showAlert('error', 'Please select an account for each entry with amount');
                return false;
            }

            debitSum += debitAmount;
            creditSum += creditAmount;
        });

        // Update totals
        $('#debit_sum_div').text(`Rs ${debitSum.toFixed(2)}`);
        $('#credit_sum_div').text(`Rs ${creditSum.toFixed(2)}`);

        const balance = Math.abs(debitSum - creditSum);
        $('#balance_amount').text(`Rs ${balance.toFixed(2)}`);

        // Update balance alert
        const balanceAlert = $('#balance_alert');
        if (balance === 0 && debitSum > 0 && creditSum > 0) {
            balanceAlert.removeClass('alert-info alert-warning').addClass('alert-success');
            balanceAlert.find('small').html('<i class="bi bi-check-circle me-1"></i>Balanced: <span id="balance_amount">Rs 0.00</span>');
        } else if (balance > 0) {
            balanceAlert.removeClass('alert-info alert-success').addClass('alert-warning');
            balanceAlert.find('small').html('<i class="bi bi-exclamation-triangle me-1"></i>Unbalanced: <span id="balance_amount">Rs ' + balance.toFixed(2) + '</span>');
        } else {
            balanceAlert.removeClass('alert-success alert-warning').addClass('alert-info');
            balanceAlert.find('small').html('<i class="bi bi-info-circle me-1"></i>Balance: <span id="balance_amount">Rs 0.00</span>');
        }

        // Enable/disable submit button
        const canSubmit = !hasError && debitSum === creditSum && debitSum > 0;
        $('#transaction_btn').prop('disabled', !canSubmit);

        updateDescription();
    }

    /**
     * Update description automatically
     */
    function updateDescription() {
        let description = "";
        let fromCounter = 0;
        let toCounter = 0;

        $('.party-row').each(function() {
            const debitAmount = parseFloat($(this).find('.debit-amount').val()) || 0;
            const creditAmount = parseFloat($(this).find('.credit-amount').val()) || 0;
            const vendorSelect = $(this).find('.vendor-dropdown');

            if (vendorSelect[0].selectedIndex > 0) {
                const selectedOption = vendorSelect[0].options[vendorSelect[0].selectedIndex];
                const vendorName = selectedOption.getAttribute('data-name');

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

        $('.description').val(description.trim());
    }

    /**
     * Submit journal entries
     */
    function submitJournalEntries() {
        const submitBtn = $('#transaction_btn');
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Please wait...');

        const entries = [];
        let hasError = false;

        $('.party-row').each(function() {
            const debitAmount = parseFloat($(this).find('.debit-amount').val()) || 0;
            const creditAmount = parseFloat($(this).find('.credit-amount').val()) || 0;
            const vendorSelect = $(this).find('.vendor-dropdown');
            const description = $(this).find('.description').val();

            if (debitAmount > 0 || creditAmount > 0) {
                if (!vendorSelect.val()) {
                    hasError = true;
                    return false;
                }

                const selectedOption = vendorSelect[0].options[vendorSelect[0].selectedIndex];

                entries.push({
                    vendor_id_from: vendorSelect.val(),
                    vendor_from_type: selectedOption.getAttribute('data-type'),
                    journal_amount: debitAmount > 0 ? debitAmount : creditAmount,
                    journal_description: description,
                    journal_date: $('#journal_date').val(),
                    debit_credit: debitAmount > 0 ? '2' : '1', // 2=debit, 1=credit
                    voucher_id: null // Will be set after first entry gets voucher_id
                });
            }
        });

        if (hasError || entries.length === 0) {
            showAlert('error', 'Please complete all entries properly');
            submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Submit Journal Entry');
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
            showAlert('success', 'Journal entries saved successfully!');
            $('#journalModal').modal('hide');
            setTimeout(() => {
                location.reload();
            }, 1500);
            return;
        }

        const entry = entries[index];

        $.ajax({
            url: window.routes.store,
            type: 'POST',
            data: {
                ...entry,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Store voucher_id from first entry for subsequent entries
                    if (index === 0 && response.voucher_id) {
                        currentVoucherId = response.voucher_id;
 add                        // Update all remaining entries with the same voucher_id
                        for (let i = index + 1; i < entries.length; i++) {
                            entries[i].voucher_id = response.voucher_id;
                        }
                    }

                    // Submit next entry
                    submitEntries(entries, index + 1, submitBtn);
                } else {
                    showAlert('error', response.message || 'Failed to save entry');
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Submit Journal Entry');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('error', response?.message || 'Failed to save entry');
                submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Submit Journal Entry');
            }
        });
    }

    // Confirm delete - perform AJAX delete
    $(document).on('click', '#confirmDeleteBtn', function() {
        const id = $('#delete_entry_id').val();

        // Button loading state
        const btn = $('#confirmDeleteBtn');
        btn.attr('disabled', true);
        btn.find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: window.routes.delete.replace(':id', id),
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#deleteModal').modal('hide');
                    showAlert('success', response.message || 'Deleted successfully');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('error', response.message || 'Please try again');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('error', response?.message || 'An error occurred. Please try again.');
            },
            complete: function() {
                btn.attr('disabled', false);
                btn.find('.spinner-border').addClass('d-none');
            }
        });
    });

    /**
     * Load voucher details for delete confirmation
     */
    function loadVoucherDetails(entryId) {
        $.ajax({
            url: window.routes.getVoucherDetails.replace(':id', entryId),
            type: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    $('#voucher-id-display').text(response.voucher_id);
                    $('#total-entries-display').text(response.total_entries);

                    // Build entries list
                    let entriesHtml = '<div class="list-group">';
                    response.entries.forEach(function(entry) {
                        const type = entry.debit_credit == 2 ? 'Debit' : 'Credit';
                        const amount = parseFloat(entry.amount).toFixed(2);
                        entriesHtml += `
                            <div class="list-group-item py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${entry.vendor_name}</strong>
                                        <br><small class="text-muted">${entry.description}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge ${entry.debit_credit == 2 ? 'bg-success' : 'bg-danger'}">${type}</span>
                                        <br><strong>Rs ${amount}</strong>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    entriesHtml += '</div>';

                    $('#entries-list').html(entriesHtml);
                    $('#voucher-details').show();
                } else {
                    $('#voucher-details').hide();
                }
            },
            error: function() {
                $('#voucher-details').hide();
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
});
