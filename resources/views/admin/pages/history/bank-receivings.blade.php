@extends('admin.layout.master')

@section('title', 'Bank Receivings History')
@section('description', 'Bank Receivings History and Transaction Details')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/history.css') }}">
@endsection

@section('content')
@permission('history.bank-receivings.view')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-bank2 text-primary me-2"></i>Bank Receivings History</h3>
            <p class="text-muted mb-0">Bank receiving records from {{ date('d-m-Y', strtotime($startDate)) }} to {{ date('d-m-Y', strtotime($endDate)) }}</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.history.bank-receivings') }}" method="get" class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.history.bank-receivings') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Receivings Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="bi bi-bank2 me-2"></i>Bank Receivings</h5>
                    <div class="dt-buttons-container"></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered history-table" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Bank <small style="font-size: 10px">(Debit)</small></th>
                                    <th class="text-center">Account <small style="font-size: 10px">(Credit)</small></th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Cheque Number</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processedReceivings as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ date('d-m-Y', strtotime($transaction->transaction_date)) }}</td>
                                        <td>
                                            @if($transaction->payment_type == 1)
                                                <span class="badge bg-info">Cash</span>
                                            @else
                                                <span class="badge bg-primary">{{ $transaction->bank ? $transaction->bank->name : 'Unknown Bank' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $transaction->vendor->vendor_name }}
                                            <span class="badge bg-secondary">{{ $transaction->vendor->vendor_type }}</span>
                                        </td>
                                        <td>Rs {{ number_format($transaction->amount, 0, '', ',') }}</td>
                                        <td>{{ $transaction->cheque_number ?: '-' }}</td>
                                        <td>{{ $transaction->description }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                @permission('payments.bank-receiving.edit')
                                                    <a href="{{ route('payments.bank-receiving.edit-vendor', $transaction->id) }}"
                                                       class="btn btn-sm btn-outline-primary me-1"
                                                       title="Edit Vendor">
                                                        <i class="bi bi-person-gear"></i>
                                                    </a>
                                                @endpermission
                                                @permission('payments.transaction.delete')
                                                    @php
                                                        $ledgerPurchaseType = app('App\\Http\\Controllers\\PaymentController')->getLedgerPurchaseType($transaction->transaction_type, $transaction->payment_type);
                                                    @endphp
                                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-id="{{ $transaction->id }}"
                                                        data-ledger-type="{{ $ledgerPurchaseType }}"
                                                        title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endpermission
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th class="text-center">Rs {{ number_format($receivingTotals->total_amount, 0, '', ',') }}</th>
                                    <th colspan="2">-</th>
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
<script src="{{ asset('js/history-ajax.js') }}?v=4.1"></script>
@endpush
