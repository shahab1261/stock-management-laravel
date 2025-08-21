/**
 * Dips Management JavaScript
 * Handles dip entry form and validation
 */

$(document).ready(function() {
    // Initialize dips functionality
    initializeDipsPage();

    /**
     * Initialize dips page functionality
     */
    function initializeDipsPage() {
        // Add new dip button
        $('#addDipBtn').click(function() {
            $('#dipModal').modal('show');
            resetForm();
        });

        // Form submission
        $('#dipEntryForm').submit(function(e) {
            e.preventDefault();
            submitDipEntry();
        });

        // Delete dip entry - open confirmation modal
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            $('#delete_dip_id').val(id);
            $('#deleteModal').modal('show');
        });

        // Dip value change - get liters from dip chart
        $('#dip_value').change(function() {
            const dipValue = $(this).val();
            const tankId = $('#tank_list').val();

            if (!tankId) {
                showAlert('info', 'Please select tank first');
                $('#dip_value').val('');
                return;
            }

            if (dipValue) {
                getDipLiters(tankId, dipValue);
            } else {
                $('#liter_value').val('');
            }
        });

        // Tank selection change
        $('#tank_list').change(function() {
            const tankId = $(this).val();
            if (tankId) {
                // Clear dip value and liters when tank changes
                $('#dip_value').val('');
                $('#liter_value').val('');
            }
        });

        // Confirm delete button
        $('#confirmDeleteBtn').click(function() {
            const id = $('#delete_dip_id').val();
            deleteDip(id);
        });
    }

    /**
     * Reset form to initial state
     */
    function resetForm() {
        $('#dipEntryForm')[0].reset();
        clearValidationErrors();
        $('#liter_value').val('');

        // Reset button state
        const btn = $('#dipBtn');
        btn.prop('disabled', false);
        btn.find('.spinner-border').addClass('d-none');
        $('#dipBtnText').text('Submit');
    }

    /**
     * Submit dip entry
     */
    function submitDipEntry() {
        const submitBtn = $('#dipBtn');
        const btnText = $('#dipBtnText');
        const spinner = submitBtn.find('.spinner-border');

        // Set loading state
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.text('Please wait...');

        // Clear previous errors
        clearValidationErrors();

        // Get form data
        const formData = {
            tank_id: $('#tank_list').val(),
            dip_value: $('#dip_value').val(),
            liter_value: $('#liter_value').val(),
            dip_date: $('#dip_date').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Validate liters value
        if (!formData.liter_value || formData.liter_value === '0') {
            showAlert('error', 'Dip not found in dip chart of this tank');
            resetButtonState(submitBtn, spinner, btnText);
            return;
        }

        // Submit form
        $.ajax({
            url: window.routes.store,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Dip added successfully');
                    $('#dipModal').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', response.message || 'Failed to add dip');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    displayValidationErrors(response.errors);
                } else {
                    showAlert('error', response?.message || 'An error occurred. Please try again.');
                }
            },
            complete: function() {
                resetButtonState(submitBtn, spinner, btnText);
            }
        });
    }

    /**
     * Get liters from dip chart based on dip value
     */
    function getDipLiters(tankId, dipValue) {
        $.ajax({
            url: window.routes.getLiters,
            type: 'POST',
            data: {
                tank_id: tankId,
                dip_value: dipValue,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const liters = parseFloat(response.data.liters.replace(/,/g, ''));
                    $('#liter_value').val(liters);
                } else {
                    $('#liter_value').val('');
                    showAlert('info', response.message || 'Invalid dip mm');
                }
            },
            error: function() {
                $('#liter_value').val('');
                showAlert('error', 'Error fetching dip data');
            }
        });
    }

    /**
     * Delete dip entry
     */
    function deleteDip(id) {
        const btn = $('#confirmDeleteBtn');
        const spinner = btn.find('.spinner-border');

        // Set loading state
        btn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: window.routes.delete,
            type: 'DELETE',
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    showAlert('success', response.message || 'Dip deleted successfully');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('error', response.message || 'Failed to delete dip');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('error', response?.message || 'An error occurred. Please try again.');
            },
            complete: function() {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    }

    /**
     * Reset button state
     */
    function resetButtonState(btn, spinner, btnText) {
        btn.prop('disabled', false);
        spinner.addClass('d-none');
        btnText.text('Submit');
    }

    /**
     * Display validation errors
     */
    function displayValidationErrors(errors) {
        $.each(errors, function(field, messages) {
            const input = $(`#${field}`);
            const errorDiv = $(`#${field}-error`);

            input.addClass('is-invalid');
            errorDiv.text(messages[0]).show();
        });
    }

    /**
     * Clear validation errors
     */
    function clearValidationErrors() {
        $('.form-control, .form-select').removeClass('is-invalid is-valid');
        $('.invalid-feedback').hide().text('');
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
     * Format number for display
     */
    function formatNumber(number, decimals = 2) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    }

    /**
     * Validate form before submission
     */
    function validateForm() {
        let isValid = true;
        const requiredFields = ['tank_list', 'dip_value', 'liter_value', 'dip_date'];

        requiredFields.forEach(function(field) {
            const input = $(`#${field}`);
            const value = input.val();

            if (!value || value.trim() === '') {
                input.addClass('is-invalid');
                $(`#${field}-error`).text('This field is required').show();
                isValid = false;
            } else {
                input.removeClass('is-invalid').addClass('is-valid');
                $(`#${field}-error`).hide();
            }
        });

        return isValid;
    }

    /**
     * Handle modal events
     */
    $('#dipModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    $('#deleteModal').on('hidden.bs.modal', function() {
        $('#delete_dip_id').val('');
    });

    // Real-time validation
    $('.form-control, .form-select').on('input change', function() {
        const field = $(this);
        const value = field.val();

        if (value && value.trim() !== '') {
            field.removeClass('is-invalid').addClass('is-valid');
            $(`#${field.attr('id')}-error`).hide();
        }
    });

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+N for new dip
        if (e.ctrlKey && e.which === 78) {
            e.preventDefault();
            $('#addDipBtn').click();
        }

        // Escape to close modals
        if (e.which === 27) {
            $('.modal').modal('hide');
        }
    });

    // Window resize handler for responsive tables
    $(window).resize(function() {
        if ($.fn.DataTable.isDataTable('#dipsTable')) {
            $('#dipsTable').DataTable().columns.adjust().responsive.recalc();
        }
    });

    // Auto-refresh functionality (optional)
    function autoRefresh() {
        // Uncomment if you want auto-refresh every 5 minutes
        // setInterval(function() {
        //     if (!$('.modal').hasClass('show')) {
        //         location.reload();
        //     }
        // }, 300000); // 5 minutes
    }

    // Initialize auto-refresh
    // autoRefresh();
});

/**
 * Global functions that can be called from outside
 */
window.DipsPage = {
    addNew: function() {
        $('#addDipBtn').click();
    },

    refresh: function() {
        location.reload();
    },

    deleteDip: function(id) {
        $('#delete_dip_id').val(id);
        $('#deleteModal').modal('show');
    }
};
