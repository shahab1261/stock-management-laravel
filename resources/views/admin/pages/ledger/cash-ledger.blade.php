@extends('admin.layout.master')

@section('title', 'Cash Ledger')
@section('description', 'Cash Ledger and Transaction Details')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/history.css') }}">
@endsection

@section('content')
@permission('ledger.cash.view')
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3 class="mb-0"><i class="bi bi-cash-stack text-primary me-2"></i>Cash Ledger</h3>
                <p class="text-muted mb-0">Cash ledger records from {{ date('d-m-Y', strtotime($startDate)) }} to
                    {{ date('d-m-Y', strtotime($endDate)) }}</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
                        @if ($openingBalance && $openingBalance->debit != $openingBalance->credit)
                            @php
                                $finalBalance = $openingBalance->final_balance;
                            @endphp
                            <div class="cash-balance-widget d-flex align-items-center">
                                <div class="cash-balance-icon me-3">
                                    <i class="bi bi-cash-coin text-primary fs-4"></i>
                                </div>
                                <div class="cash-balance-content">
                                    <small class="text-muted d-block mb-0">Opening Balance</small>
                                    <span class="cash-balance-amount text-primary fw-bold">Rs
                                        {{ number_format(abs($finalBalance)) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.ledger.cash') }}" method="get" class="row align-items-end">
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $endDate }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('admin.ledger.cash') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Details Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Cash Ledger Details</h5>
                        <div class="d-flex gap-2">
                            <button type="button" id="printBtn" class="btn btn-primary d-flex align-items-center">
                                <i class="bi bi-printer me-2"></i> Print
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered history-table" id="ledger_table" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Details/Notes</th>
                                        <th class="text-center">Debit</th>
                                        <th class="text-center">Credit</th>
                                        <th class="text-center">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $creditSum = 0;
                                        $debitSum = 0;
                                        $finalBalance = 0;
                                        $totalEntries = 1;
                                    @endphp

                                    {{-- @if ($openingBalance && $openingBalance->debit != $openingBalance->credit)
                                @php
                                    $finalBalance = $openingBalance->final_balance;
                                @endphp
                                <tr class="table-primary">
                                    <td class="text-center">-</td>
                                    <td>Opening Balance</td>
                                    <td class="text-center">
                                        @if ($openingBalance->debit > $openingBalance->credit)
                                            <span class="transaction-debit">
                                                 {{ number_format(abs($openingBalance->final_balance)) }}
                                            </span>
                                            @php $debitSum += abs($openingBalance->final_balance); @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($openingBalance->credit > $openingBalance->debit)
                                            <span class="transaction-credit">
                                                 {{ number_format(abs($openingBalance->final_balance)) }}
                                            </span>
                                            @php $creditSum += abs($openingBalance->final_balance); @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center fw-medium">
                                            {{ number_format($openingBalance->final_balance) }}
                                    </td>
                                </tr>
                            @endif --}}

                                    @foreach ($ledgers as $ledger)
                                        @php
                                            $noteRate = app(
                                                'App\Http\Controllers\LedgerController',
                                            )->getLedgerTransactionDetail(
                                                $ledger->transaction_id,
                                                $ledger->purchase_type,
                                                $ledger->vendor_type,
                                                $ledger->vendor_id,
                                            );
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($ledger->transaction_date)->format('Y-m-d') }}
                                            </td>
                                            <td>
                                                <small>{{ $noteRate }} {{ $ledger->tarnsaction_comment }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if ($ledger->transaction_type == 2 || $ledger->transaction_type == '2')
                                                    <span class="transaction-debit">
                                                         {{ number_format($ledger->amount) }}
                                                    </span>
                                                    @php
                                                        $debitSum += $ledger->amount;
                                                        $finalBalance += $ledger->amount;
                                                    @endphp
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($ledger->transaction_type == 1 || $ledger->transaction_type == '1')
                                                    <span class="transaction-credit">
                                                         {{ number_format($ledger->amount) }}
                                                    </span>
                                                    @php
                                                        $finalBalance -= $ledger->amount;
                                                        $creditSum += $ledger->amount;
                                                    @endphp
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center fw-medium">
                                                 {{ number_format($finalBalance) }}
                                            </td>
                                        </tr>
                                        @php $totalEntries++; @endphp
                                    @endforeach

                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th class="text-center">-</th>
                                        <th>Total</th>
                                        <th class="text-center">
                                            {{-- <small>Rs</small> --}}
                                            {{ number_format($debitSum) }}
                                        </th>
                                        <th class="text-center">
                                            {{-- <small>Rs</small> --}}
                                            {{ number_format($creditSum) }}
                                        </th>
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
    <script src="{{ asset('js/ledger.js') }}"></script>
@endpush
