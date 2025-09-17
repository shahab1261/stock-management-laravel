@extends('admin.layout.master')

@section('title', 'Add Sales')
@section('description', 'Create a new sales record')

@section('content')
@permission('sales.create')
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3 class="mb-0"><i class="bi bi-cart-plus text-primary me-2"></i>Add New Sales</h3>
                <p class="text-muted mb-0">Create a new sales record with product details</p>
            </div>
        </div>

        <!-- Main Form -->
        <form id="salesForm" action="#" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Sales Information Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-cart-plus-fill text-primary fs-4 me-2"></i>
                        <h2 class="fs-5 fw-bold mb-0">Sales Information</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Sale Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-calendar3"></i>
                                </span>
                                <input type="date" class="form-control border-start-0" id="sale_date" name="sale_date"
                                    value="{{ \Carbon\Carbon::parse($settings->date_lock ?? now())->format('Y-m-d') }}"
                                    readonly max="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Select Vendor</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-shop"></i>
                                </span>
                                <select class="form-select border-start-0 searchable-dropdown" name="supplier_id" id="supplier_id" required>
                                    <option selected disabled>Select Vendor</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}"
                                            data-type="1">{{ str_replace('&amp;', '&', $supplier->name) }} (Supplier)
                                        </option>
                                    @endforeach
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" data-name="{{ $customer->name }}"
                                            data-type="2">{{ str_replace('&amp;', '&', $customer->name) }} (Customer)
                                        </option>
                                    @endforeach
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-type="3">
                                            {{ str_replace('&amp;', '&', $product->name) }} (Product)</option>
                                    @endforeach
                                    @foreach ($expenses as $expense)
                                        <option value="{{ $expense->id }}" data-name="{{ $expense->expense_name }}"
                                            data-type="4">{{ str_replace('&amp;', '&', $expense->expense_name) }}
                                            (Expense)</option>
                                    @endforeach
                                    @foreach ($incomes as $income)
                                        <option value="{{ $income->id }}" data-name="{{ $income->income_name }}"
                                            data-type="5">{{ str_replace('&amp;', '&', $income->income_name) }} (Income)
                                        </option>
                                    @endforeach
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->id }}" data-name="{{ $bank->name }}"
                                            data-type="6">{{ str_replace('&amp;', '&', $bank->name) }} (Bank)</option>
                                    @endforeach
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                            data-type="9">{{ str_replace('&amp;', '&', $employee->name) }} (Employee)
                                        </option>
                                    @endforeach
                                    <option value="7" data-name="cash" data-type="7">Cash</option>
                                    <option value="8" data-name="mp" data-type="8">Mp</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Select Product</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-box"></i>
                                </span>
                                <select class="form-select border-start-0 searchable-dropdown" name="product_id" id="product_id" required>
                                    <option selected disabled>Select Product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Quantity</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-layers"></i>
                                </span>
                                <input type="number" name="quantity" id="quantity" class="form-control border-start-0"
                                    step="0.01" value="0" min="0">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Rate</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-currency-dollar"></i>
                                </span>
                                <input type="number" name="rate" id="rate" class="form-control border-start-0"
                                    value="0" min="1" step="0.01">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-cash"></i>
                                </span>
                                <input type="number" name="amount" id="amount" class="form-control border-start-0"
                                    value="0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Freight</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-truck"></i>
                                </span>
                                <select class="form-select border-start-0 searchable-dropdown" id="freight">
                                    <option value="0" selected>Without Freight</option>
                                    <option value="1">With Freight</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Freight Charges</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-currency-dollar"></i>
                                </span>
                                <input type="number" name="freight_charges" id="freight_charges"
                                    class="form-control border-start-0" value="0" min="0" step="1">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Select Transport</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-truck"></i>
                                </span>
                                <select class="form-select border-start-0 searchable-dropdown" name="tank_lari_id" id="tank_lari_id"
                                    required>
                                    <option selected disabled>Select Transport</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->larry_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Sale From</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-building"></i>
                                </span>
                                <select class="form-select border-start-0 searchable-dropdown" id="sales_type">
                                    <option value="1" selected>Goddam</option>
                                    <option value="2">Direct</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Select Tank</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-fuel-pump"></i>
                                </span>
                                <select class="form-select border-start-0" id="selected_tank" required>
                                    <!-- Tank options will be populated dynamically -->
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-medium text-muted mb-2">Notes</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-chat-square-text"></i>
                                </span>
                                <textarea class="form-control border-start-0" name="notes" id="notes" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mb-4">
                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
                <button type="submit" id="add_sales_btn" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i>Submit
                </button>
            </div>
        </form>
    </div>
@endpermission

    @push('scripts')
        <script src="{{ asset('js/sales-ajax.js') }}"></script>
    @endpush
@endsection
