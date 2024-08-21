@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Yeni transfer əlavə et</h3>

    <div class="flex flex-col mt-8">
        <form action="{{ route('dashboard.transfer') }}" method="post">
            @csrf
            <div class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="from_warehouse">Anbardan</label>
                    <select id="from_warehouse" name="from_warehouse"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option selected>Anbar seçin</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-gray-700" for="to_warehouse">Anbara</label>
                    <select id="to_warehouse" name="to_warehouse"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option selected>Anbar seçin</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-gray-700" for="products">Məhsul</label>
                    <select id="products" name="product_id"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @isset($products)
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div>
                    <label class="text-gray-700" for="quantity">Sayı</label>
                    <input name="quantity"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        type="number">
                </div>
                <div>
                    <label class="text-gray-700" for="transfer_date">Transfer tarixi</label>
                    <input name="transfer_date"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        type="datetime-local">
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 text-gray-200 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Yarat</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        document.getElementById('from_warehouse').addEventListener('change', function() {
            var warehouseId = this.value;

            if (warehouseId) {
                fetch(`/get-products/${warehouseId}`)
                    .then(response => response.json())
                    .then(data => {
                        var productsSelect = document.getElementById('products');
                        productsSelect.innerHTML = '<option selected>Məhsul seçin</option>'; // Reset options

                        data.forEach(function(product) {
                            var option = document.createElement('option');
                            option.value = product.product_id;
                            option.textContent = `${product.product_name} (${product.quantity})`;
                            productsSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching products:', error));
            } else {
                document.getElementById('products').innerHTML = '<option selected>Məhsul seçin</option>';
            }
        });
    </script>
@endsection
