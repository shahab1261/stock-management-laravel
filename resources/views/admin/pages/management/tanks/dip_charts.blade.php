@extends('admin.layout.master')

@section('title', 'Dip Charts - ' . $tank->tank_name)
@section('description', 'View dip charts for ' . $tank->tank_name)

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0" style="font-size: 36px;">
                        <i class="bi bi-rulers text-primary me-2"></i>Dip Charts for "{{ $tank->tank_name }}"
                    </h3>
                    <p class="text-muted mb-0">
                        Product: <span class="fw-medium">{{ $tank->product->name ?? 'N/A' }}</span> |
                        Capacity: <span class="fw-medium">{{ number_format($tank->tank_limit, 2) }} liters</span>
                    </p>
                </div>
                <a href="{{ route('admin.tanks.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Tanks
                </a>
            </div>
        </div>
    </div>

    <!-- Dip Charts Table Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Dip Chart Measurements</h5>
                    <div class="d-flex gap-2">
                        <button id="printBtn" class="btn btn-outline-primary d-none">
                            <i class="bi bi-printer me-1"></i> Print
                        </button>
                        <button id="exportBtn" class="btn btn-outline-success">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export to CSV
                        </button>
                        @if($dipCharts->count() > 0)
                        <button id="deleteDipChartsBtn" class="btn btn-outline-danger" data-tank-id="{{ $tank->id }}" data-tank-name="{{ $tank->tank_name }}">
                            <i class="bi bi-trash me-1"></i> Delete Dip Chart
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body" id="dipChartsCardBody">
                    @if($dipCharts->count() > 0)
                        <div class="table-responsive">
                            <table id="dipChartsTable" class="table table-hover table-bordered align-middle mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%" class="text-center">#</th>
                                        <th width="35%" class="text-center">Depth (mm)</th>
                                        <th width="35%" class="text-center">Volume (liters)</th>
                                        <th width="15%" class="text-center">Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dipCharts as $chart)
                                    <tr>
                                        <td class="text-center"></td>
                                        <td class="text-center">{{ $chart->mm }}</td>
                                        <td class="text-center">{{ $chart->liters }}</td>
                                        <td class="text-center">{{ date('d M Y', strtotime($chart->created_at)) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <div class="mb-3">
                                <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="mb-1">No Dip Charts Found</h5>
                            <p class="text-muted mb-4">There are no dip chart records available for this tank.</p>
                            <a href="{{ route('admin.tanks.index') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Tanks
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Information Card -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About Dip Charts</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Dip charts help to determine the volume of liquid in a tank by measuring the depth of the liquid.
                        The depth is measured using a dipstick, and the corresponding volume is read from the chart.
                    </p>
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div>
                                <i class="bi bi-lightbulb fs-4 me-3"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading mb-1">How to use a dip chart</h6>
                                <p class="mb-0 small">
                                    1. Insert a clean dipstick vertically into the tank<br>
                                    2. Remove the dipstick and note the depth measurement<br>
                                    3. Find the corresponding volume in the chart
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Visual Representation</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    @if($dipCharts->count() > 0)
                        <div style="width: 100%; height: 250px;">
                            <canvas id="dipChartGraph"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bar-chart text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No data available for visualization</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pagination {
        margin-bottom: 0;
    }
    .page-link {
        color: #4154f1;
        border-color: #e9ecef;
    }
    .page-item.active .page-link {
        background-color: #4154f1;
        border-color: #4154f1;
    }
    .alert-info {
        background-color: #f6f8ff;
        border-color: #e0e7ff;
        color: #3c50e0;
    }

    /* Table and DataTables padding */
    #dipChartsTable_wrapper {
        padding-top: 0.5rem;
    }

    #dipChartsTable_wrapper .dataTables_length,
    #dipChartsTable_wrapper .dataTables_filter {
        padding: 0.5rem 0;
        margin-bottom: 0.5rem;
    }

    #dipChartsTable_wrapper .dataTables_length {
        padding-left: 0;
    }

    #dipChartsTable_wrapper .dataTables_filter {
        padding-right: 0;
    }

    #dipChartsTable_wrapper .dataTables_info,
    #dipChartsTable_wrapper .dataTables_paginate {
        padding: 0.5rem 0;
        margin-top: 0.5rem;
    }

    #dipChartsTable_wrapper .dataTables_info {
        padding-left: 0;
    }

    #dipChartsTable_wrapper .dataTables_paginate {
        padding-right: 0;
    }

    /* Table cell padding */
    #dipChartsTable thead th {
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        vertical-align: middle;
    }

    #dipChartsTable tbody td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
    }

    /* Table responsive container */
    .table-responsive {
        border-radius: 0.375rem;
    }

    /* Card body padding for table card */
    #dipChartsCardBody {
        padding: 1rem;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTables first
        var dipChartsTable;
        if ($('#dipChartsTable').length) {
            dipChartsTable = $('#dipChartsTable').DataTable({
                processing: true,
                responsive: false,
                scrollX: true,
                dom: '<"row align-items-center"<"col-md-6 dt-left-margin"l><"col-md-6 text-end"f>>t<"row align-items-center"<"col-md-6"i><"col-md-6 text-end"p>>',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"],
                ],
                pageLength: 25,
                order: [[1, "asc"]], // Order by Depth (mm) ascending
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                    {
                        targets: "_all",
                        className: "text-center"
                    }
                ],
                drawCallback: function() {
                    // Update row numbers after each draw
                    var api = this.api();
                    api.column(0, {search: 'applied', order: 'applied'}).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });

            // Add left margin to the show entries dropdown
            setTimeout(function() {
                $('.dt-left-margin').css('padding-left', '0');
            }, 100);
        }

        @if($dipCharts->count() > 0)
        // Chart data
        const labels = [];
        const volumes = [];

        @foreach($dipCharts as $chart)
            labels.push('{{ $chart->mm }} mm');
            volumes.push({{ $chart->liters }});
        @endforeach

        // Create chart
        const ctx = document.getElementById('dipChartGraph').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Volume (liters)',
                    data: volumes,
                    borderColor: '#4154f1',
                    backgroundColor: 'rgba(65, 84, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#4154f1',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Volume (liters)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Depth (mm)'
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
        @endif

        // Print functionality (hidden for now)
        $('#printBtn').on('click', function() {
            window.print();
        });

        // Export to CSV functionality
        $('#exportBtn').on('click', function() {
            if (dipChartsTable) {
                // Get all data from DataTable (including filtered/sorted data)
                const data = dipChartsTable.rows({search: 'applied'}).data().toArray();

                // Prepare CSV content
                let csvContent = '#,Depth (mm),Volume (liters),Date Created\n';

                data.forEach(function(row, index) {
                    // Escape commas and quotes in data
                    const escapeCSV = function(field) {
                        if (field === null || field === undefined) {
                            return '';
                        }
                        const stringField = String(field);
                        // If field contains comma, quote, or newline, wrap in quotes and escape quotes
                        if (stringField.includes(',') || stringField.includes('"') || stringField.includes('\n')) {
                            return '"' + stringField.replace(/"/g, '""') + '"';
                        }
                        return stringField;
                    };

                    const rowData = [
                        index + 1,
                        escapeCSV(row[1]), // Depth (mm)
                        escapeCSV(row[2]), // Volume (liters)
                        escapeCSV(row[3])  // Date Created
                    ];
                    csvContent += rowData.join(',') + '\n';
                });

                // Create blob and download
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'DipCharts_{{ $tank->tank_name }}.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });

        // Delete all dip charts functionality
        $('#deleteDipChartsBtn').on('click', function() {
            const tankId = $(this).data('tank-id');
            const tankName = $(this).data('tank-name');
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const deleteUrl = '{{ route("admin.tanks.dip_charts.delete", ":id") }}'.replace(':id', tankId);

            Swal.fire({
                title: 'Are you sure?',
                text: `This will delete ALL dip chart records for "${tankName}". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete all!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the dip charts.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message || 'All dip charts deleted successfully',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#4154f1'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Redirect to tanks index
                                        window.location.href = '{{ route("admin.tanks.index") }}';
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete dip charts',
                                    confirmButtonColor: '#4154f1'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Failed to delete dip charts. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonColor: '#4154f1'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
