{{-- @dd($expenses) --}}
<!-- Suppliers -->
{{-- <optgroup label="Suppliers"> --}}
@foreach ($suppliers as $supplier)
    <option value="{{ $supplier->id }}" data-type="1" data-name="{{ $supplier->name }}">{{ $supplier->name }} (Supplier)</option>
@endforeach
{{-- </optgroup> --}}

<!-- Customers -->
{{-- <optgroup label="Customers"> --}}
@foreach ($customers as $customer)
    <option value="{{ $customer->id }}" data-type="2" data-name="{{ $customer->name }}">{{ $customer->name }} (Customer)</option>
@endforeach
{{-- </optgroup> --}}

<!-- Products -->
{{-- <optgroup label="Products"> --}}
@foreach ($products as $product)
    <option value="{{ $product->id }}" data-type="3" data-name="{{ $product->name }}">{{ $product->name }} (Product)</option>
@endforeach
{{-- </optgroup> --}}

<!-- Expenses -->
{{-- <optgroup label="Expenses"> --}}
@foreach ($expenses as $expense)
    <option value="{{ $expense->id }}" data-type="4" data-name="{{ $expense->expense_name }}">
        {{ $expense->expense_name }} (Expense)</option>
@endforeach
{{-- </optgroup> --}}

<!-- Incomes -->
{{-- <optgroup label="Incomes"> --}}
@foreach ($incomes as $income)
    <option value="{{ $income->id }}" data-type="5" data-name="{{ $income->income_name }}">
        {{ $income->income_name }} (Income)</option>
@endforeach
@foreach ($banks as $bank)
    <option value="{{ $bank->id }}" data-name="{{ $bank->name }}" data-type="6">
        {{ str_replace('&amp;', '&', $bank->name) }} (Bank)</option>
@endforeach
@foreach ($employees as $employee)
    <option value="{{ $employee->id }}" data-name="{{ $employee->name }}" data-type="9">
        {{ str_replace('&amp;', '&', $employee->name) }} (Employee)</option>
@endforeach
<option value="7" data-name="cash" data-type="7">Cash</option>
<option value="8" data-name="mp" data-type="8">Mp</option>
{{-- </optgroup> --}}
