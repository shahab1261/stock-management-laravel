<!-- Suppliers -->
{{-- <optgroup label="Suppliers"> --}}
    @foreach($suppliers as $supplier)
        <option value="{{ $supplier->id }}" data-type="1" data-name="{{ $supplier->name }}">{{ $supplier->name }}</option>
    @endforeach
{{-- </optgroup> --}}

<!-- Customers -->
{{-- <optgroup label="Customers"> --}}
    @foreach($customers as $customer)
        <option value="{{ $customer->id }}" data-type="2" data-name="{{ $customer->name }}">{{ $customer->name }}</option>
    @endforeach
{{-- </optgroup> --}}

<!-- Products -->
{{-- <optgroup label="Products"> --}}
    @foreach($products as $product)
        <option value="{{ $product->id }}" data-type="3" data-name="{{ $product->name }}">{{ $product->name }}</option>
    @endforeach
{{-- </optgroup> --}}

<!-- Expenses -->
{{-- <optgroup label="Expenses"> --}}
    @foreach($expenses as $expense)
        <option value="{{ $expense->eid }}" data-type="4" data-name="{{ $expense->expense_name }}">{{ $expense->expense_name }}</option>
    @endforeach
{{-- </optgroup> --}}

<!-- Incomes -->
{{-- <optgroup label="Incomes"> --}}
    @foreach($incomes as $income)
        <option value="{{ $income->id }}" data-type="5" data-name="{{ $income->income_name }}">{{ $income->income_name }}</option>
    @endforeach
{{-- </optgroup> --}}
