@extends('layouts.master')

@section('body')
<div class="container mx-auto">
    <h3 class="text-gray-700 text-3xl font-medium">Anbara çıxışın düzəlişi</h3>

    <div class="flex flex-col mt-8">
        <form action="{{ route('dashboard.exits.update', $exit->id) }}" method="post">
            @csrf
            @method('PUT')
            <div id="mainDiv" class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="from_warehouse">Anbardan</label>
                    <select id="from_warehouse" name="from_warehouse"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach ($warehouses as $warehouse)
                            <option {{ $exit->from_warehouse_id == $warehouse->id ? 'selected' :  '' }} value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-gray-700" for="to_warehouse">Anbara</label>
                    <select id="to_warehouse" name="to_warehouse"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach ($warehouses as $warehouse)
                            <option {{ $exit->to_warehouse_id == $warehouse->id ? 'selected' :  '' }} value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="relative flex justify-between flex-col md:flex-row mt-2 pt-8">
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="products">Məhsul</label>
                        <select id="products" name="product"
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            {{-- @isset($products)
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            @endisset --}}
                        </select>
                    </div>
    
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="quantity">Sayı</label>
                        <input name="quantity"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number" value="{{ old('quantity', $exit->quantity) }}">
                    </div>
    
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="transfer_date">Transfer tarixi</label>
                        <input name="transfer_date"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="datetime-local" value="{{ old('entry_date', $exit->entry_date) }}">
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
    function setDefaultWarehouse(){
        const mainWarehouseId = {{$exit->from_warehouse_id}}
        
        if(mainWarehouseId){
            fetch(`/get-products/${mainWarehouseId}`)
                    .then(response => response.json())
                    .then(data => {
                        var productsSelect = [...document.querySelectorAll('.product-input')];
                        productsSelect.forEach(products => {
                            products.innerHTML = '';
                            data.forEach(function(product) {
                                // if(product.quantity){
                                    var option = document.createElement('option');
                                    option.value = product.product_id;
                                    option.textContent = `${product.product_name} ${product.product_code ? `- ${product.product_code}` : ''} - ${product.category_name} (${product.quantity})`;
                                    products.appendChild(option);
                                // }
                            });
                            let selectedProductValue = {{$exit->product_id}};
                            products.value = selectedProductValue;
                        })
                    })
                    .catch(error => console.error('Error fetching products:', error));
        }else{
            document.getElementById('products').innerHTML = '';
            html = '';
        }
    }
    setDefaultWarehouse();
    $('#products').select2();
    // const selectedProductId = {{$exit->product_id}};
    // $('#products').val(selectedProductId).trigger('change');
    document.getElementById('from_warehouse').addEventListener('change', function() {
        var warehouseId = this.value;

        if (warehouseId) {
            fetch(`/get-products/${warehouseId}`)
                .then(response => response.json())
                .then(data => {
                    var productsSelect = [...document.querySelectorAll('.product-input')];
                    productsSelect.forEach(products => {
                        products.innerHTML = '';
                        // html = '';
                        data.forEach(function(product) {
                            // html += `<option value="${product.product_id}">${product.product_name} ${product.product_code ? `- ${product.product_code}` : ''} - ${product.category_name} (${product.quantity})</option>`
                            var option = document.createElement('option');
                            option.value = product.product_id;
                            option.textContent = `${product.product_name} ${product.product_code ? `- ${product.product_code}` : ''} - ${product.category_name} (${product.quantity})`;
                            products.appendChild(option);
                        });
                    })

                })
                .catch(error => console.error('Error fetching products:', error));
        } else {
            document.getElementById('products').innerHTML = '';
            html = '';
        }
    });
</script>
@endsection
