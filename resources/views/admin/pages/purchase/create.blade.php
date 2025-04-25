@extends('admin.layout.master')

@section('title', 'Add Purchase')
@section('description', 'Create a new purchase record')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    {{-- <div class="d-flex align-items-center mb-4">
        <div class="position-relative">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-cart-plus fs-3 text-primary"></i>
            </div>
        </div>
        <div class="ms-3">
            <h1 class="fs-3 fw-bold mb-1">Add New Purchase</h1>
            <p class="text-muted mb-0">Create a new purchase record with product details</p>
        </div>
    </div> --}}
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="mb-0"><i class="bi bi-cart-plus text-primary me-2"></i>Add New Purchase</h3>
            <p class="text-muted mb-0">Create a new purchase record with product details</p>
        </div>
    </div>

    <!-- Main Form -->
    <form id="purchaseForm" action="{{ route('purchase.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Purchase Information Card -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-cart-check-fill text-primary fs-4 me-2"></i>
                    <h2 class="fs-5 fw-bold mb-0">Purchase Information</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Purchase Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-calendar3"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" name="purchase_date"
                                value="{{ \Carbon\Carbon::parse($settings->date_lock ?? now())->format('d/m/Y') }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Select Vendor</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-shop"></i>
                            </span>
                            <select class="form-select border-start-0" name="vendor_id" id="vendor" required>
                                <option selected disabled>Select Vendor</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ str_replace('&amp;', '&', $supplier->name) }} (Supplier)</option>
                                @endforeach
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ str_replace('&amp;', '&', $customer->name) }} (Customer)</option>
                                @endforeach
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ str_replace('&amp;', '&', $product->name) }} (Product)</option>
                                @endforeach
                                @foreach ($expenses as $expense)
                                    <option value="{{ $expense->eid }}">{{ str_replace('&amp;', '&', $expense->expense_name) }} (Expense)</option>
                                @endforeach
                                @foreach ($incomes as $income)
                                    <option value="{{ $income->id }}">{{ str_replace('&amp;', '&', $income->income_name) }} (Income)</option>
                                @endforeach
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ str_replace('&amp;', '&', $bank->name) }} (Bank)</option>
                                @endforeach
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ str_replace('&amp;', '&', $employee->name) }} (Employee)</option>
                                @endforeach
                                <option value="7">Cash</option>
                                <option value="8">Mp</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Select Product</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-box"></i>
                            </span>
                            <select class="form-select border-start-0" name="product_id" id="product" required>
                                <option selected disabled>Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Stock</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-layers"></i>
                            </span>
                            <input type="number" name="stock" id="stock" class="form-control border-start-0" step="0.01" value="0" min="0">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Rate</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-currency-dollar"></i>
                            </span>
                            <input type="number" name="rate" id="rate" class="form-control border-start-0" value="0" min="0" step="0.01" disabled>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-cash"></i>
                            </span>
                            <input type="number" name="amount" id="amount" class="form-control border-start-0" value="0" readonly>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Select Vehicle</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-truck"></i>
                            </span>
                            <select class="form-select border-start-0" name="vehicle_id" id="vehicle_chamber">
                                <option selected disabled>Select Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->larry_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Driver</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <select class="form-select border-start-0" name="driver_id">
                                <option selected disabled>Select Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->driver_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Terminals</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-building"></i>
                            </span>
                            <select class="form-select border-start-0" name="terminal_id">
                                <option selected disabled>Select Terminal</option>
                                @foreach($terminals as $terminal)
                                    <option value="{{ $terminal->id }}" data-name="{{ $terminal->name }}">{{ $terminal->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-3"  style="width: 18em;">
                        <label class="form-label fw-medium text-muted mb-2">Tanks</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-fuel-pump"></i>
                            </span>
                            <select class="form-select border-start-0" name="tank_id" id="tank_update">

                            </select>
                        </div>
                    </div>
                    <div class="col-md-5" style="width: 20em;">
                        <label class="form-label fw-medium text-muted mb-2">Upload Receipt</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-file-earmark-text"></i>
                            </span>
                            <input type="file" class="form-control border-start-0" name="receipt">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Comments</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-chat-square-text"></i>
                            </span>
                            <textarea class="form-control border-start-0" name="comments" rows="1"></textarea>
                            <button type="button" id="showChamberBtn" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chambers Section (Hidden by Default) -->
        <div id="chambersSection" class="card border-0 shadow-sm rounded-3 mb-4" style="display: none;">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-layers-half text-primary fs-4 me-2"></i>
                    <h2 class="fs-5 fw-bold mb-0">Chambers Information</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Fuel Type Selection -->
                <div class="mb-4">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="fuel_type" id="super" value="super" checked>
                        <label class="form-check-label" for="super">Super</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="fuel_type" id="diesel" value="diesel">
                        <label class="form-check-label" for="diesel">Diesel</label>
                    </div>
                </div>

                <!-- Chambers Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-hover border bg-white rounded-3">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 px-3 border-0">Chamber #</th>
                                <th class="py-2 px-3 border-0">Capacity (ltr)</th>
                                <th class="py-2 px-3 border-0">Dip</th>
                                <th class="py-2 px-3 border-0">Rec. dip</th>
                                <th class="py-2 px-3 border-0">Gain/Loss</th>
                                <th class="py-2 px-3 border-0">Ltr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Chamber 1 -->
                            <tr>
                                <td class="p-2">
                                    <input type="text" class="form-control form-control-sm bg-light shadow-none" name="chamber[1][number]" value="1" readonly>
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-capacity" id="chamber_capacity_one" name="chamber[1][capacity]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-dip" id="chamber_dip_one" name="chamber[1][dip]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-rec-dip" name="chamber[1][rec_dip]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-gain-loss" name="chamber[1][gain_loss]" value="0" readonly>
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-ltr" name="chamber[1][ltr]" value="0">
                                </td>
                            </tr>

                            <!-- Chamber 2 -->
                            <tr>
                                <td class="p-2">
                                    <input type="text" class="form-control form-control-sm bg-light shadow-none" name="chamber[2][number]" value="2" readonly>
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-capacity" id="chamber_capacity_two" name="chamber[2][capacity]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-dip" id="chamber_dip_two" name="chamber[2][dip]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-rec-dip" name="chamber[2][rec_dip]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-gain-loss" name="chamber[2][gain_loss]" value="0" readonly>
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-ltr" name="chamber[2][ltr]" value="0">
                                </td>
                            </tr>

                            <!-- Chamber 3 -->
                            <tr>
                                <td class="p-2">
                                    <input type="text" class="form-control form-control-sm bg-light shadow-none" name="chamber[3][number]" value="3" readonly>
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-capacity" id="chamber_capacity_three" name="chamber[3][capacity]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-dip" id="chamber_dip_three" name="chamber[3][dip]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-rec-dip" name="chamber[3][rec_dip]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-gain-loss" name="chamber[3][gain_loss]" value="0" readonly>
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-ltr" name="chamber[3][ltr]" value="0">
                                </td>
                            </tr>

                            <!-- Chamber 4 -->
                            <tr>
                                <td class="p-2">
                                    <input type="text" class="form-control form-control-sm bg-light shadow-none" name="chamber[4][number]" value="4" readonly>
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-capacity" id="chamber_capacity_four" name="chamber[4][capacity]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-dip" id="chamber_dip_four" name="chamber[4][dip]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-rec-dip" name="chamber[4][rec_dip]" value="0">
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-gain-loss" name="chamber[4][gain_loss]" value="0" readonly>
                                </td>
                                <td class="p-2">
                                    <input type="number" class="form-control form-control-sm shadow-none chamber-ltr" name="chamber[4][ltr]" value="0">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Temperature and Loss/Gain Calculations -->
                <div class="row g-4 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Invoice Temp Degree</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-thermometer"></i>
                            </span>
                            <input type="number" class="form-control border-start-0" name="invoice_temp" value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Rec. Temp Degree</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-thermometer-half"></i>
                            </span>
                            <input type="number" class="form-control border-start-0" name="rec_temp" value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Temp Loss/Gain Degree</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-thermometer-sun"></i>
                            </span>
                            <input type="number" class="form-control border-start-0" name="temp_loss_gain" value="0" readonly>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Dip Loss/Gain Ltr</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-droplet"></i>
                            </span>
                            <input type="number" class="form-control border-start-0" name="dip_loss_gain" value="0" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Loss/Gain by Temperature</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-thermometer-low"></i>
                            </span>
                            <input type="number" class="form-control border-start-0" name="loss_gain_temp" value="0" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium text-muted mb-2">Actual Short Loss/Gain</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-graph-up-arrow"></i>
                            </span>
                            <input type="number" class="form-control border-start-0" name="actual_short_loss_gain" value="0" readonly>
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
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i>Save Purchase
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#vehicle_chamber').on('change', function(){
            let tank_id = $(this).find('option:selected').val();

            $.ajax({
                url: "{{ route('tank.chamber.data') }}",
                method: 'POST',
                data: {
                    _token:  "{{ csrf_token() }}",
                    tank_id: tank_id,
                },
                success:function(response){
                    if(response.data){
                        const data = response.data;
                        // console.log(data);
                            $('#chamber_capacity_one').val(data[0].chamber_capacity_one);
                            $('#chamber_dip_one').val(data[0].chamber_dip_one);
                            $('#chamber_capacity_two').val(data[0].chamber_capacity_two);
                            $('#chamber_dip_two').val(data[0].chamber_dip_two);
                            $('#chamber_capacity_three').val(data[0].chamber_capacity_three);
                            $('#chamber_dip_three').val(data[0].chamber_dip_three);
                            $('#chamber_capacity_four').val(data[0].chamber_capacity_four);
                            $('#chamber_dip_four').val(data[0].chamber_dip_four);
                    } else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonColor: '#4154f1'
                        });
                    }
                }, error: function(xhr, error){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonColor: '#4154f1'
                    });
                }
            });
        });

        $('#product').on('change', function(){
            const selectedOption = $(this).find('option:selected');
            const productId = selectedOption.val();

            $.ajax({
                url: "{{ route('product.tank.update') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: productId,
                },
                success: function(response) {
                    if(response.tanks){
                        const tanks = response.tanks;
                        console.log(tanks);
                        $('#tank_update').empty().append('<option selected disabled>Select Tank</option>');
                        $.each(tanks, function (key, value){
                            $("#tank_update").append(`<option value="${value.id}">${value.tank_name}</option>`);
                        });
                    } else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonColor: '#4154f1'
                        });
                    }
                }, error: function(xhr, error){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonColor: '#4154f1'
                    });
                }
            });
        });

        $('#product').on('change', function(){
            const selectedOption = $(this).find('option:selected');
            const productId = selectedOption.val();

            $.ajax({
                url: "{{ route('product.rate.update') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: productId,
                },
                success: function(response) {
                    if(response.rate){
                        $('#rate').val(response.rate);
                    } else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonColor: '#4154f1'
                        });
                    }
                }, error: function(xhr, error){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonColor: '#4154f1'
                    });
                }
            });
        });

        $('#showChamberBtn').click(function() {
            $('#chambersSection').slideToggle(300);
            $(this).find('i').toggleClass('bi-plus-circle bi-dash-circle');
        });

        $('input[name="rate"], input[name="stock"]').on('input', function() {
            const rate = parseFloat($('input[name="rate"]').val()) || 0;
            const stock = parseFloat($('input[name="stock"]').val()) || 0;
            $('input[name="amount"]').val((rate * stock).toFixed(2));
        });

        $('.chamber-dip, .chamber-rec-dip').on('input', function() {
            const row = $(this).closest('tr');
            const dip = parseFloat(row.find('.chamber-dip').val()) || 0;
            const recDip = parseFloat(row.find('.chamber-rec-dip').val()) || 0;
            row.find('.chamber-gain-loss').val((recDip - dip).toFixed(2));

            calculateTotalDipLossGain();
        });

        $('input[name="invoice_temp"], input[name="rec_temp"]').on('input', function() {
            const invoiceTemp = parseFloat($('input[name="invoice_temp"]').val()) || 0;
            const recTemp = parseFloat($('input[name="rec_temp"]').val()) || 0;
            $('input[name="temp_loss_gain"]').val((recTemp - invoiceTemp).toFixed(2));

            calculateLossGainByTemp();
        });

        function calculateTotalDipLossGain() {
            let totalDipLossGain = 0;
            $('.chamber-gain-loss').each(function() {
                totalDipLossGain += parseFloat($(this).val()) || 0;
            });
            $('input[name="dip_loss_gain"]').val(totalDipLossGain.toFixed(2));

            calculateActualShortLossGain();
        }

        function calculateLossGainByTemp() {
            const tempLossGain = parseFloat($('input[name="temp_loss_gain"]').val()) || 0;
            const stock = parseFloat($('input[name="stock"]').val()) || 0;
            const lossGainByTemp = tempLossGain * stock * 0.01;
            $('input[name="loss_gain_temp"]').val(lossGainByTemp.toFixed(2));

            calculateActualShortLossGain();
        }

        function calculateActualShortLossGain() {
            const dipLossGain = parseFloat($('input[name="dip_loss_gain"]').val()) || 0;
            const lossGainByTemp = parseFloat($('input[name="loss_gain_temp"]').val()) || 0;
            $('input[name="actual_short_loss_gain"]').val((dipLossGain + lossGainByTemp).toFixed(2));
        }

        $('.form-control, .form-select').hover(
            function() { $(this).addClass('border-primary'); },
            function() { $(this).removeClass('border-primary'); }
        );

        $('#purchaseForm').on('submit', function(e) {
            e.preventDefault();

            // Basic validation
            if ($('#vendor').val() === null || $('#product').val() === null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select Vendor and Product'
                });
                return false;
            }

            // Submit form if validation passes
            this.submit();
        });
    });
</script>
@endpush
@endsection
