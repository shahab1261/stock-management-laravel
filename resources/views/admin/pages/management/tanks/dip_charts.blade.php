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
                        <button id="printBtn" class="btn btn-outline-primary">
                            <i class="bi bi-printer me-1"></i> Print
                        </button>
                        <button id="exportBtn" class="btn btn-outline-success">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export to Excel
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($dipCharts->count() > 0)
                        <div class="table-responsive">
                            <table id="dipChartsTable" class="table table-hover table-bordered align-middle mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%" class="ps-3">#</th>
                                        <th width="35%" class="ps-3">Depth (mm)</th>
                                        <th width="35%" class="ps-3">Volume (liters)</th>
                                        <th width="15%" class="ps-3">Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dipCharts as $index => $chart)
                                    <tr>
                                        <td class="ps-3">{{ $dipCharts->firstItem() + $index }}</td>
                                        <td class="ps-3">{{ $chart->mm }}</td>
                                        <td class="ps-3">{{ $chart->liters }}</td>
                                        <td class="ps-3">{{ date('d M Y', strtotime($chart->created_at)) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center px-3 py-3">
                            <div>
                                Showing {{ $dipCharts->firstItem() }} to {{ $dipCharts->lastItem() }} of {{ $dipCharts->total() }} entries
                            </div>
                            <div>
                                {{ $dipCharts->links('pagination::bootstrap-4') }}
                            </div>
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
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    $(document).ready(function() {
        @if($dipCharts->count() > 0)
        // Chart data
        const labels = [];
        const volumes = [];

        @foreach($dipCharts as $chart)
            labels.push('{{ $chart->depth }} mm');
            volumes.push({{ $chart->volume }});
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

        // Print functionality
        $('#printBtn').on('click', function() {
            window.print();
        });

        // Export to Excel functionality
        $('#exportBtn').on('click', function() {
            const data = [
                ['#', 'Depth (mm)', 'Volume (liters)', 'Date Created'],
                @foreach($dipCharts as $index => $chart)
                [{{ $dipCharts->firstItem() + $index }}, {{ $chart->depth }}, {{ $chart->volume }}, '{{ date('d/m/Y', strtotime($chart->created_at)) }}'],
                @endforeach
            ];

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(data);

            // Style the header row
            const header = {
                font: { bold: true },
                fill: { fgColor: { rgb: "EFEFEF" } }
            };

            // Apply styles to header row
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let col = range.s.c; col <= range.e.c; col++) {
                const cell = XLSX.utils.encode_cell({ r: 0, c: col });
                if (!ws[cell]) ws[cell] = {};
                ws[cell].s = header;
            }

            XLSX.utils.book_append_sheet(wb, ws, 'Dip Charts - {{ $tank->tank_name }}');
            XLSX.writeFile(wb, 'DipCharts_{{ $tank->tank_name }}.xlsx');
        });
    });
</script>
@endpush
