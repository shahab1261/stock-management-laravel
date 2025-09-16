<div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
    <h5 class="mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Sales Summary</h5>
    {{-- <button class="btn btn-sm btn-outline-primary print-section" data-section="sales-summary-section">
        <i class="bi bi-printer me-1"></i>Print
    </button> --}}
</div>

<div id="sales-summary-section" class="table-responsive">
    <table class="table table-hover table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th class="text-center">Product Name</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesSummary as $sale)
                <tr>
                    <td class="fw-medium">{{ $sale->product_name }}</td>
                    <td class="text-center">{{ number_format($sale->total_quantity, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                    <td class="text-center"><span class="fw-medium">Rs {{ number_format($sale->total_amount, 0, '', ',') }}</span></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr class="fw-bold">
                <td>Total</td>
                <td class="text-center">{{ number_format($salesSummaryTotals->total_quantity, 0, '', ',') }} <small class="text-muted">ltr</small></td>
                <td class="text-center text-success">Rs {{ number_format($salesSummaryTotals->total_amount, 0, '', ',') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
