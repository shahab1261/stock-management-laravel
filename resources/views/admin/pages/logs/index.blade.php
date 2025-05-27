@extends('admin.layout.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-activity text-primary me-2"></i>System Activity Logs</h5>
                    <div class="d-flex align-items-center">
                        <select id="actionTypeFilter" class="form-select" style="width: 150px;">
                            <option value="">All Actions</option>
                            <option value="create">Create</option>
                            <option value="update">Update</option>
                            <option value="delete">Delete</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="logsTable" class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Date & Time</th>
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
                                        <span class="badge bg-{{ $log->action_type === 'create' ? 'success' : ($log->action_type === 'update' ? 'primary' : ($log->action_type === 'delete' ? 'danger' : 'info')) }} bg-opacity-10 text-{{ $log->action_type === 'create' ? 'success' : ($log->action_type === 'update' ? 'primary' : ($log->action_type === 'delete' ? 'danger' : 'info')) }} px-3 py-2">
                                            <i class="bi bi-{{ $log->action_type === 'create' ? 'plus' : ($log->action_type === 'update' ? 'pencil' : ($log->action_type === 'delete' ? 'trash' : 'info-circle')) }} me-1"></i>
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
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6 text-end"f>>t<"row mt-3"<"col-md-6"i><"col-md-6 text-end"p>>',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    });

    // Action type filter
    $('#actionTypeFilter').on('change', function() {
        const actionType = this.value;
        table.column(2).search(actionType).draw();
    });
});
</script>
@endpush
@endsection
