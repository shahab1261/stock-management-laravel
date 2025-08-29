@extends('admin.layout.master')

@section('content')
@permission('logs.view')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-activity text-primary me-2"></i>System Activity Logs</h5>
                    <div class="d-flex align-items-center">
                        <select id="actionTypeFilter" class="form-select" style="width: 150px;">
                            <option value="">All Actions</option>
                            <option value="Create">Create</option>
                            <option value="Update">Update</option>
                            <option value="Delete">Delete</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="logsTable" class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">User</th>
                                    <th class="text-center">Action</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td class="text-center">{{ $log->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <span class="text-primary fw-medium" style="font-size: 14px;">{{ substr($log->user->name, 0, 1) }}</span>
                                            </div>
                                            <span class="fw-medium">{{ $log->user->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->action_type === 'Create' ? 'success' : ($log->action_type === 'Update' ? 'primary' : ($log->action_type === 'Delete' ? 'danger' : 'info')) }} bg-opacity-10 text-{{ $log->action_type === 'Create' ? 'success' : ($log->action_type === 'Update' ? 'primary' : ($log->action_type === 'Delete' ? 'danger' : 'info')) }} px-3 py-2">
                                            <i class="bi bi-{{ $log->action_type === 'Create' ? 'plus' : ($log->action_type === 'Update' ? 'pencil' : ($log->action_type === 'Delete' ? 'trash' : 'info-circle')) }} me-1"></i>
                                            {{ ucfirst($log->action_type) }}
                                        </span>
                                    </td>
                                    <td class="text-gray-600">{{ $log->action_description }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $log->created_at->format('Y-m-d') }}</span>
                                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
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
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#logsTable').DataTable({
        responsive: false,
        scrollX: false,
        searching: true,
        ordering: true,
        paging: true,
        info: true,
        pageLength: 25, // Default to 25 records per page
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>t<"row mt-3"<"col-md-6"i><"col-md-6 text-end"p>>',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[4, 'desc']], // Sort by date column (column 4) in descending order by default
        language: {
            search: "Search logs:",
            lengthMenu: "Show _MENU_ logs per page",
            info: "Showing _START_ to _END_ of _TOTAL_ logs",
            infoEmpty: "Showing 0 to 0 of 0 logs",
            infoFiltered: "(filtered from _MAX_ total logs)",
            emptyTable: "No logs found",
            zeroRecords: "No matching logs found"
        }
    });

    // Action type filter
    $('#actionTypeFilter').on('change', function() {
        const actionType = this.value;
        if (actionType === '') {
            table.column(2).search('').draw();
        } else {
            // Search for the action type in the action column (column 2)
            // Using regex to match the exact action type
            table.column(2).search('\\b' + actionType + '\\b', true, false).draw();
        }
    });
});
</script>
@endpush
@endsection
