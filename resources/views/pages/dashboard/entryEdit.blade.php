@extends('layouts.master')

@section('body')
<div class="container mx-auto">
    <h3 class="text-gray-700 text-3xl font-medium">Anbara girişin düzəlişi</h3>

    <div class="flex flex-col mt-8">
        <form action="{{ route('dashboard.entries.update', $entry->id) }}" method="post">
            @csrf
            @method('PUT')
            <div id="mainDiv" class="grid grid-cols-1 mt-4">
                
                <div>
                    <label class="text-gray-700" for="warehouse">Anbar</label>
                    <input list="warehouses" id="warehouse" name="warehouse_name"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        type="text" autocomplete="off" value="{{ old('warehouse_name', $warehouseName) }}">
                    <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{ old('warehouse_id', $entry->to_warehouse_id) }}">
                    <datalist id="warehouses">
                        @foreach ($warehouses as $warehouse)
                            <option data-id="{{ $warehouse->id }}" value="{{ $warehouse->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="text-gray-700" for="company">Şirkət</label>
                    <input list="companies" id="company" name="company_name"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        type="text" autocomplete="off" value="{{ old('company_name', $companyName) }}">
                    <input type="hidden" id="company_id" name="company_id" value="{{ old('company_id', $entry->company_id) }}">
                    <datalist id="companies">
                        @foreach ($companies as $company)
                            <option data-id="{{ $company->id }}" value="{{ $company->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="relative flex justify-between flex-col md:flex-row mt-2 pt-8">
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="product">Məhsul</label>
                        <input list="products" id="product" name="product"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off" value="{{ old('product', $productName) }}">
                        <datalist id="products">
                            @foreach ($products as $product)
                                <option data-code="{{ $product->code }}" value="{{ $product->name }}">
                                    {{ $product->name }} {{ $product->code ? '- ' . $product->code : '' }}
                                </option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="product_code">Məhsulun kodu</label>
                        <input id="product_code" name="product_code"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off" value="{{ old('product_code', $productCode) }}">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="quantity">Sayı</label>
                        <input name="quantity"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number" value="{{ old('quantity', $entry->quantity) }}">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="measure">Ölçü vahidi</label>
                        <input name="measure"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" value="{{ old('measure', $entry->measure) }}">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="shelf">Rəf</label>
                        <input name="shelf"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" value="{{ old('shelf', $entry->shelf) }}">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="category">Kateqoriya</label>
                        <input list="categories" id="category" name="category"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off" value="{{ old('category', $categoryName) }}">
                        <datalist id="categories">
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="entry_date">Giriş tarixi</label>
                        <input name="entry_date"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="datetime-local" value="{{ old('entry_date', $entry->entry_date) }}">
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 text-gray-200 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Yarat</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
    <script>

        const productInput = document.getElementById('product')
        const productOptions = [...document.getElementById('products').options];
        productInput.addEventListener('input', function(event){
            const selectedProduct = event.target.value;
            let foundCode = '';
            productOptions.forEach(option => {
                if (option.value === selectedProduct) {
                    foundCode = option.getAttribute('data-code');
                }
            });

            const productCodeInput = event.target.parentNode.nextElementSibling.querySelector('#product_code');
            if (productCodeInput) {
                productCodeInput.value = foundCode || '';
            }
        })

        document.getElementById('warehouse').addEventListener('input', function() {
            // Get the list of options from the datalist
            const options = document.getElementById('warehouses').options;
            const warehouseInput = document.getElementById('warehouse');
            const hiddenWarehouseId = document.getElementById('warehouse_id');

            // Loop through options to find the selected warehouse name and get its ID
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === warehouseInput.value) {
                    // Set the hidden input field with the corresponding warehouse ID
                    hiddenWarehouseId.value = options[i].getAttribute('data-id');
                    break;
                } else {
                    // Clear the hidden input if no match is found
                    hiddenWarehouseId.value = '';
                }
            }
        });

        document.getElementById('company').addEventListener('input', function() {
            // Get the list of options from the datalist
            const options = document.getElementById('companies').options;
            const companyInput = document.getElementById('company');
            const hiddenCompanyId = document.getElementById('company_id');

            for (let i = 0; i < options.length; i++) {
                if (options[i].value === companyInput.value) {
                    hiddenCompanyId.value = options[i].getAttribute('data-id');
                    break;
                } else {
                    hiddenCompanyId.value = '';
                }
            }
        });
    </script>
@endsection
