<div class="d-flex justify-content-between align-items-center mb-4" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125); padding-bottom: 10px;">
    <h5 class="mb-0"><i class="bi bi-droplet me-2 text-primary"></i>Wet Stock</h5>
    {{-- <button class="btn btn-sm btn-outline-primary print-section" data-section="wet-stock-section">
        <i class="bi bi-printer me-1"></i>Print
    </button> --}}
</div>

<div id="wet-stock-section" class="table-responsive">
    <table class="table table-hover table-bordered daybook-table" style="width:100%">
        <thead class="table-light">
            <tr>
                <th class="text-center">Date</th>
                <th class="text-center">Tank</th>
                <th class="text-center">O/Stock</th>
                <th class="text-center">Purchase</th>
                <th class="text-center">Sales</th>
                <th class="text-center">Book Stock</th>
                <th class="text-center">Dip Value (mm)</th>
                <th class="text-center">Dip Stock</th>
                <th class="text-center">Gain/Loss</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedWetStock as $wetStock)
                <tr>
                    <td>{{ date('d-m-Y', strtotime($wetStock['date'])) }}</td>
                    <td>{{ $wetStock['tank_name'] }}</td>
                    <td>{{ number_format($wetStock['opening_stock'], 0, '', ',') }} <small>ltr</small></td>
                    <td>{{ number_format($wetStock['purchase'], 0, '', ',') }} <small>ltr</small></td>
                    <td>{{ number_format($wetStock['sales'], 0, '', ',') }} <small>ltr</small></td>
                    <td>{{ number_format($wetStock['book_stock'], 0, '', ',') }} <small>ltr</small></td>
                    <td>{{ $wetStock['dip_value'] }} <small>mm</small></td>
                    <td>{{ number_format($wetStock['dip_stock'], 0, '', ',') }} <small>ltr</small></td>
                    <td>
                        @if($wetStock['gain_loss'] > 0)
                            <span class="text-success fw-bold">+{{ number_format($wetStock['gain_loss'], 0, '', ',') }}</span>
                        @elseif($wetStock['gain_loss'] < 0)
                            <span class="text-danger fw-bold">{{ number_format($wetStock['gain_loss'], 0, '', ',') }}</span>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                        <small>ltr</small>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr class="fw-bold">
                <th colspan="3" class="text-end">Total:</th>
                <th class="text-center">{{ number_format($wetStockTotals->total_purchase, 0, '', ',') }} <small>ltr</small></th>
                <th class="text-center">{{ number_format($wetStockTotals->total_sales, 0, '', ',') }} <small>ltr</small></th>
                <th class="text-center">-</th>
                <th class="text-center">-</th>
                <th class="text-center">-</th>
                <th class="text-center">
                    @if($wetStockTotals->total_gain_loss > 0)
                        <span class="text-success">+{{ number_format($wetStockTotals->total_gain_loss, 0, '', ',') }}</span>
                    @elseif($wetStockTotals->total_gain_loss < 0)
                        <span class="text-danger">{{ number_format($wetStockTotals->total_gain_loss, 0, '', ',') }}</span>
                    @else
                        <span class="text-muted">0</span>
                    @endif
                    <small>ltr</small>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
