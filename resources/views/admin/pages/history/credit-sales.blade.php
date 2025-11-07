@extends('admin.layout.master')

@section('title', 'Credit Sales History')
@section('description', 'Credit Sales History and Details')

@section('css')
<link rel="stylesheet" href="{{ asset('css/credit-sales-history.css') }}">
@endsection

@section('content')
@permission('history.credit-sales.view')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Credit Sales History</h3>
            <p class="text-muted mb-0">Records from {{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }}</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.history.credit-sales') }}" method="get" class="row align-items-end">
                        <div class="col-md-3 mb-3" style="width: 202px;">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3 mb-3" style="width: 202px;">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.history.credit-sales') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Credit Sales Details</h5>
                    <div class="dt-buttons-container"></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered history-table" id="credit-sales-history-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Vendor</th>
                                    <th class="text-center">Product</th>
                                    <th class="text-center">Tank Lorry</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Rate</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processedCreditSales as $row)
                                    <tr>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ date('d-m-Y', strtotime($row->transaction_date)) }}</td>
                                        <td>
                                            {{ $row->vendor->vendor_name }}
                                            <span class="badge bg-secondary">{{ $row->vendor->vendor_type }}</span>
                                        </td>
                                        <td>{{ $row->product ? $row->product->name : 'Not found / deleted' }}</td>
                                        <td>{{ $row->tank_lorry ? $row->tank_lorry->larry_name : 'No tank lorry found' }}</td>
                                        <td>{{ number_format($row->quantity, 0, '', ',') }} <small>ltr</small></td>
                                        <td>Rs {{ number_format($row->rate, 2) }}</td>
                                        <td>Rs {{ number_format($row->amount, 0, '', ',') }}</td>
                                        <td>{{ $row->notes }}</td>
                                        <td class="text-center">
                                            @permission('sales.credit.delete')
                                                <button class="btn btn-sm btn-danger delete-credit-sale-btn" data-ledgerpurchasetype="12" data-id="{{ $row->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endpermission
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Total:</th>
                                    <th class="text-center">{{ number_format($totals->total_quantity, 0, '', ',') }} <small>ltr</small></th>
                                    <th class="text-center">-</th>
                                    <th class="text-center"><small>Rs</small> {{ number_format($totals->total_amount, 0, '', ',') }}</th>
                                    <th class="text-center">-</th>
                                    <th class="text-center">-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endpermission
@endsection

@push('scripts')
<script src="{{ asset('js/credit-sales-history.js') }}"></script>
@endpush


