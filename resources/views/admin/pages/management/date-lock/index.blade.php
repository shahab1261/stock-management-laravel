@extends('admin.layout.master')

@section('title', 'Date Lock Management')
@section('description', 'Manage system date lock settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-lock text-primary me-2"></i>Date Lock Management</h3>
            <p class="text-muted mb-0">Manage system date lock to prevent modifications of entries before specified date</p>
        </div>
    </div>

    <!-- Date Lock Form Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-calendar-lock me-2"></i>System Date Lock</h5>
                </div>
                <div class="card-body p-4">
                    @permission('management.date-lock.edit')
                    <form id="dateLockForm" action="{{ route('admin.management.date-lock.update') }}" method="POST" class="row g-3">
                        @csrf

                        <!-- Date Lock Information -->
                            {{-- <div class="col-12 mb-3">
                                <div class="alert alert-info border-0 shadow-sm">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-info-circle me-2 mt-1"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">About Date Lock</h6>
                                            <p class="mb-0 small">
                                                The date lock prevents users from creating, editing, or deleting entries before the specified date.
                                                This helps maintain data integrity and prevents unauthorized changes to historical records.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                        <!-- Current Date Lock Display -->
                        @if(isset($settings) && $settings->date_lock)
                        <div class="col-12 mb-3">
                            <div class="alert alert-warning border-0 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-lock me-2"></i>
                                    <div>
                                        <strong>Current Date Lock:</strong>
                                        {{ \Carbon\Carbon::parse($settings->date_lock)->format('d M Y') }}
                                        {{-- <small class="text-muted ms-2">({{ $settings->date_lock }})</small> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Date Lock Input -->
                        <div class="col-md-6">
                            <label for="date_lock" class="form-label fw-medium">
                                Set Date Lock <span class="text-danger">*</span>
                                <i class="bi bi-question-circle ms-1" data-bs-toggle="tooltip"
                                   title="Entries before this date cannot be modified"></i>
                            </label>
                            <div class="input-group mb-0">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-calendar-lock me-1"></i>
                                    {{-- <i class="bi bi-lock me-1 ms-1"></i> --}}
                                </span>
                                <input type="date" class="form-control border-start-0" id="date_lock" name="date_lock"
                                    value="{{ $settings->date_lock ?? '' }}"
                                    max="{{ date('Y-m-d') }}"
                                    @if(!$hasSystemLockedPermission)
                                        data-allowed-dates="{{ json_encode([date('Y-m-d'), $settings->date_lock ?? date('Y-m-d')]) }}"
                                    @endif
                                    required>
                            </div>
                            @if($hasSystemLockedPermission)
                                <div class="form-text">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    You have full permissions to set any date lock
                                </div>
                            @else
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    You can only set the date lock to either <strong>today's date</strong> or the <strong>current system locked date</strong>
                                </div>
                            @endif
                            <div class="invalid-feedback" id="date_lock-error"></div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">
                                        <i class="bi bi-shield-lock text-primary me-1"></i>
                                        Permission Levels
                                    </h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li class="mb-1">
                                            <i class="bi bi-dot"></i>
                                            <strong>System Locked Permission:</strong> Can set any date
                                        </li>
                                        <li class="mb-1">
                                            <i class="bi bi-dot"></i>
                                            <strong>Regular Users:</strong> Limited to today or current lock date
                                        </li>
                                        <li>
                                            <i class="bi bi-dot"></i>
                                            <strong>Effect:</strong> Prevents modifications before set date
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4" id="updateDateLockBtn">
                                <i class="bi bi-save me-2"></i>Update Date Lock
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-lock display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Access Denied</h5>
                        <p class="text-muted">You don't have permission to edit date lock settings.</p>
                    </div>
                    @endpermission
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Submit form via AJAX
        $('#dateLockForm').on('submit', function(e) {
            e.preventDefault();

            // Additional validation for date lock
            var dateLockInput = $('#date_lock');
            var allowedDates = dateLockInput.data('allowed-dates');
            if (allowedDates && allowedDates.length > 0) {
                var selectedDate = dateLockInput.val();
                if (selectedDate && !allowedDates.includes(selectedDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date Selection',
                        html: 'You can only select one of these dates:<br><strong>' + allowedDates.join('</strong> or <strong>') + '</strong>',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            }

            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#updateDateLockBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...').attr('disabled', true);
                    $('.is-invalid').removeClass('is-invalid');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    $('#updateDateLockBtn').html('<i class="bi bi-save me-2"></i>Update Date Lock').attr('disabled', false);

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key).addClass('is-invalid');
                            $('#' + key + '-error').text(value[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                complete: function() {
                    $('#updateDateLockBtn').html('<i class="bi bi-save me-2"></i>Update Date Lock').attr('disabled', false);
                }
            });
        });

        // Validate date lock selection for users without system_locked permission
        $('#date_lock').on('change input', function() {
            var allowedDates = $(this).data('allowed-dates');
            if (allowedDates && allowedDates.length > 0) {
                var selectedDate = $(this).val();
                if (selectedDate && !allowedDates.includes(selectedDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date Selection',
                        html: 'You can only select one of these dates:<br><strong>' + allowedDates.join('</strong> or <strong>') + '</strong>',
                        confirmButtonText: 'OK'
                    });
                    // Reset to current value
                    var currentValue = '{{ $settings->date_lock ?? date("Y-m-d") }}';
                    $(this).val(currentValue);
                }
            }
        });

    });
</script>
@endpush
