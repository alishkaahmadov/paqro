@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Yeni şosse daxil et</h3>

    <div class="flex flex-col mt-8">
        <form action="{{ route('highways.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="warehouse_id">Anbar</label>
                    <select id="warehouse_id" name="warehouse_id" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
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
                    <label class="text-gray-700" for="code">Şosse nömrəsi</label>
                    <input name="code" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="text">
                </div>
                <div>
                    <label class="text-gray-700" for="quantity">Sayı</label>
                    <input name="quantity" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="number">
                </div>
                <div>
                    <label class="text-gray-700" for="pdf">PDF faylı</label>
                    <input name="pdf" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="file" accept="application/pdf">
                </div>
                <div>
                    <label class="text-gray-700" for="date">Tarix</label>
                    <input name="date" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="datetime-local">
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-gray-200 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Yarat</button>
            </div>
        </form>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection

@section('script')
    <script>
        document.getElementById('warehouse_id').addEventListener('change', function() {
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