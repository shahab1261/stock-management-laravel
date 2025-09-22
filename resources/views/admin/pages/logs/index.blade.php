@extends('admin.layout.master')

@section('content')
@permission('logs.view')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-activity text-primary me-2"></i>System Activity Logs</h5>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0"><i class="bi bi-funnel text-primary me-2"></i>Filter Options</h6>
                </div>
                <div class="card-body">
                    <form id="filtersForm">
                        <div class="row g-3">
                            <!-- User Filter -->
                            <div class="col-md-3">
                                <label for="userFilter" class="form-label fw-medium">
                                    <i class="bi bi-person text-primary me-1"></i>User
                                </label>
                                <select id="userFilter" name="user_filter" class="form-select">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Action Filter -->
                            <div class="col-md-3">
                                <label for="actionFilter" class="form-label fw-medium">
                                    <i class="bi bi-lightning text-primary me-1"></i>Action
                                </label>
                                <select id="actionFilter" name="action_filter" class="form-select">
                                    <option value="">All Actions</option>
                                    <option value="Create">Create</option>
                                    <option value="Update">Update</option>
                                    <option value="Delete">Delete</option>
                                </select>
                            </div>

                            <!-- From Date -->
                            <div class="col-md-3">
                                <label for="dateFrom" class="form-label fw-medium">
                                    <i class="bi bi-calendar text-primary me-1"></i>From Date
                                </label>
                                <input type="date" id="dateFrom" name="date_from" class="form-control">
                            </div>

                            <!-- To Date -->
                            <div class="col-md-3">
                                <label for="dateTo" class="form-label fw-medium">
                                    <i class="bi bi-calendar-check text-primary me-1"></i>To Date
                                </label>
                                <input type="date" id="dateTo" name="date_to" class="form-control">
                            </div>
                        </div>

                        <!-- Quick Date Filters -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <label class="form-label fw-medium">
                                    <i class="bi bi-clock-history text-primary me-1"></i>Quick Filters
                                </label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-days="0">Today</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-days="1">Yesterday</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-days="7">Last 7 Days</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-days="30">Last 30 Days</button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <!-- Applied Filters Display -->
                                    <div id="appliedFilters" class="d-none">
                                        <small class="text-muted">Applied Filters:</small>
                                        <div id="filterTags" class="mt-1"></div>
                                    </div>
                                    <div class="ms-auto"></div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2">
                                        <button type="button" id="clearFilters" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-1"></i>Clear
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search me-1"></i>Apply Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Loading Overlay -->
                    <div id="tableLoadingOverlay" class="position-absolute w-100 h-100 d-none" style="top: 0; left: 0; background: rgba(255,255,255,0.8); z-index: 10;">
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <div class="mt-2 text-muted">Loading logs...</div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="logsTable" class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 80px;">#</th>
                                    <th class="text-center" style="width: 200px;">User</th>
                                    <th class="text-center" style="width: 120px;">Action</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center" style="width: 150px;">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endpermission

@push('styles')
<style>
.avatar-sm {
    width: 36px;
    height: 36px;
    font-size: 16px;
}

.badge {
    font-weight: 500;
    border-radius: 30px;
}

.table > :not(caption) > * > * {
    padding: 1rem;
}

.table tbody tr:hover {
    background-color: rgba(65, 84, 241, 0.05);
}

.filter-tag {
    display: inline-block;
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    margin: 0.125rem 0.125rem 0.125rem 0;
}

.filter-tag .btn-close {
    font-size: 0.75rem;
    margin-left: 0.5rem;
}

.table-responsive {
    position: relative;
}

.card {
    border: none !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.form-label {
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.btn-sm {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

/* Quick date buttons styling */
.quick-date.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

/* Loading animation */
@keyframes spin {
    to { transform: rotate(360deg); }
}

.spinning {
    animation: spin 1s linear infinite;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let table;

    // Initialize DataTable
    function initializeDataTable() {
        if (table) {
            table.destroy();
        }

        table = $('#logsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.logs.data") }}',
                type: 'POST',
                data: function(d) {
                    d.user_filter = $('#userFilter').val();
                    d.action_filter = $('#actionFilter').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d._token = '{{ csrf_token() }}';
                },
                beforeSend: function() {
                    $('#tableLoadingOverlay').removeClass('d-none');
                },
                complete: function() {
                    $('#tableLoadingOverlay').addClass('d-none');
                },
                error: function(xhr, error, code) {
                    $('#tableLoadingOverlay').addClass('d-none');
                    console.error('DataTables Ajax Error:', error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Loading Error',
                        text: 'Failed to load logs data. Please try again.',
                        confirmButtonText: 'Retry',
                        confirmButtonColor: '#0d6efd'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            table.ajax.reload();
                        }
                    });
                }
            },
            columns: [
                { data: 'id', name: 'id', className: 'text-center' },
                {
                    data: 'user_info',
                    name: 'user_info',
                    orderable: true,
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <span class="text-primary fw-medium" style="font-size: 14px;">${data.initial}</span>
                                </div>
                                <span class="fw-medium">${data.name}</span>
                            </div>
                        `;
                    }
                },
                {
                    data: 'action_badge',
                    name: 'action_type',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                            <span class="badge bg-${data.class} bg-opacity-10 text-${data.class} px-3 py-2">
                                <i class="bi bi-${data.icon} me-1"></i>
                                ${data.text}
                            </span>
                        `;
                    }
                },
                { data: 'action_description', name: 'action_description', className: 'text-gray-600' },
                {
                    data: 'formatted_date',
                    name: 'created_at',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex flex-column">
                                <span class="fw-medium">${data.date}</span>
                                <small class="text-muted">${data.time}</small>
                            </div>
                        `;
                    }
                }
            ],
            order: [[4, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>t<"row mt-3"<"col-md-6"i><"col-md-6 text-end"p>>',
            language: {
                search: "Search logs:",
                lengthMenu: "Show _MENU_ logs per page",
                info: "Showing _START_ to _END_ of _TOTAL_ logs",
                infoEmpty: "Showing 0 to 0 of 0 logs",
                infoFiltered: "(filtered from _MAX_ total logs)",
                emptyTable: "No logs found",
                zeroRecords: "No matching logs found",
                processing: "Loading logs..."
            },
            responsive: false,
            scrollX: true,
            autoWidth: false
        });
    }

    // Initialize the table
    initializeDataTable();

    // Apply filters
    $('#filtersForm').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
        updateAppliedFilters();
    });

    // Quick date buttons
    $('.quick-date').on('click', function() {
        const days = parseInt($(this).data('days'));
        const today = new Date();
        const targetDate = new Date(today);

        if (days === 0) {
            $('#dateFrom').val(today.toISOString().split('T')[0]);
            $('#dateTo').val(today.toISOString().split('T')[0]);
        } else if (days === 1) {
            targetDate.setDate(today.getDate() - 1);
            $('#dateFrom').val(targetDate.toISOString().split('T')[0]);
            $('#dateTo').val(targetDate.toISOString().split('T')[0]);
        } else {
            targetDate.setDate(today.getDate() - days);
            $('#dateFrom').val(targetDate.toISOString().split('T')[0]);
            $('#dateTo').val(today.toISOString().split('T')[0]);
        }

        $('.quick-date').removeClass('btn-primary').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
    });

    // Clear filters
    $('#clearFilters').on('click', function() {
        $('#filtersForm')[0].reset();
        $('.quick-date').removeClass('btn-primary').addClass('btn-outline-secondary');
        table.ajax.reload();
        updateAppliedFilters();
    });

    // Update applied filters display
    function updateAppliedFilters() {
        const filters = [];
        const $filterTags = $('#filterTags');

        const userVal = $('#userFilter').val();
        const userText = $('#userFilter option:selected').text();
        if (userVal) {
            filters.push({ type: 'user', value: userVal, text: `User: ${userText}` });
        }

        const actionVal = $('#actionFilter').val();
        if (actionVal) {
            filters.push({ type: 'action', value: actionVal, text: `Action: ${actionVal}` });
        }

        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();
        if (dateFrom && dateTo) {
            if (dateFrom === dateTo) {
                filters.push({ type: 'date', value: dateFrom, text: `Date: ${dateFrom}` });
            } else {
                filters.push({ type: 'date', value: `${dateFrom}_${dateTo}`, text: `Date: ${dateFrom} to ${dateTo}` });
            }
        } else if (dateFrom) {
            filters.push({ type: 'date', value: dateFrom, text: `From: ${dateFrom}` });
        } else if (dateTo) {
            filters.push({ type: 'date', value: dateTo, text: `Until: ${dateTo}` });
        }

        if (filters.length > 0) {
            $('#appliedFilters').removeClass('d-none');
            $filterTags.html(filters.map(filter =>
                `<span class="filter-tag">
                    ${filter.text}
                    <button type="button" class="btn-close" data-filter-type="${filter.type}"></button>
                </span>`
            ).join(''));
        } else {
            $('#appliedFilters').addClass('d-none');
        }
    }

    // Remove individual filter tags
    $(document).on('click', '.filter-tag .btn-close', function() {
        const filterType = $(this).data('filter-type');

        switch(filterType) {
            case 'user':
                $('#userFilter').val('');
                break;
            case 'action':
                $('#actionFilter').val('');
                break;
            case 'date':
                $('#dateFrom').val('');
                $('#dateTo').val('');
                $('.quick-date').removeClass('btn-primary').addClass('btn-outline-secondary');
                break;
        }

        table.ajax.reload();
        updateAppliedFilters();
    });

    // Initialize applied filters on page load
    updateAppliedFilters();
});
</script>
@endpush
@endsection
