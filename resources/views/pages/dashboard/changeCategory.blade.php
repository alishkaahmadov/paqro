@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Kateqoriyanı dəyiş</h3>
    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div>
                <form action="{{ route('listProductCategories') }}" method="get">
                    <div class="grid grid-cols-3 mt-4 gap-3 mb-4">
                        <div>
                            <label class="text-gray-700" for="warehouse">Anbar</label>
                            <select id="warehouse" name="warehouse_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected disabled>Anbar seçin</option>
                                @foreach ($warehouses as $warehouse)
                                    <option {{ $warehouse_id && $warehouse_id == $warehouse->id ? 'selected' : '' }} 
                                        value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="products">Məhsul</label>
                            <select id="products" name="product_id"
                                class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="category">Kateqoriya</label>
                            <select id="category" name="subcategory_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected disabled>Kateqoriya seçin</option>
                                @foreach ($categories as $category)
                                    <option {{ $subcategory_id && $subcategory_id == $category->id ? 'selected' : '' }}
                                        value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="self-end">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-green-500 border-2 border-green-500 text-white rounded-md hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-400">Axtar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full text-center">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Anbar
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Məhsul
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Kod
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Kateqoriya
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Qalıq
                            </th>
                        </tr>
                    </thead>
    
                    <tbody class="bg-white">
                        @foreach ($data as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$item->warehouse_name}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$item->product_name}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$item->product_code}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$item->subcategory_name}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    {{$item->quantity}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (isset($data) && count($data))
                <div>
                    <form action="{{ route('updateProductCategory') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 mt-4 gap-3 mb-4">
                            <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                            <input type="hidden" name="subcategory_id" value="{{ $subcategory_id }}">
                            <input type="hidden" name="product_id" value="{{ $product_id }}">
                            <div>
                                <label class="text-gray-700">Yeni Kateqoriya</label>
                                <select name="new_subcategory_id"
                                    class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="" selected disabled>Kateqoriya seçin</option>
                                    @foreach ($categories as $category)
                                        @if ($category->id != $subcategory_id)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="self-end">
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-blue-500 border-2 border-blue-500 text-white rounded-md hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400">Dəyiş</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
    
@endsection

@section('script')
<script>
    $(document).ready(async function() {
        const currentWarehouseId = @json($warehouse_id);
        async function setProductOptions(){
            if(currentWarehouseId){
                await fetch(`/get-products/${currentWarehouseId}`)
                    .then(response => response.json())
                    .then(data => {
                        allProductsData = [...data];
                        var productsSelect = [...document.querySelectorAll('.product-input')];
                        productsSelect.forEach(products => {
                            products.innerHTML = '<option value="" selected></option>';
                            data.forEach(function(product) {
                                var option = document.createElement('option');
                                option.value = product.product_id;
                                option.textContent = `${product.product_name} ${product.product_code ? `- ${product.product_code}` : ''} - ${product.category_name} (${product.quantity})`;
                                products.appendChild(option);
                            });
                        })
    
                    })
                    .catch(error => console.error('Error fetching products:', error));
            }
        }   
        await setProductOptions()
        $('#products').select2();
        const selectedProductIds = @json($product_id);
            $('#products').val(selectedProductIds).trigger('change');
    });
    document.getElementById('warehouse').addEventListener('change', function() {
        var warehouseId = this.value;

        if (warehouseId) {
            fetch(`/get-products/${warehouseId}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    allProductsData = [...data];
                    var productsSelect = [...document.querySelectorAll('.product-input')];
                    productsSelect.forEach(products => {
                        products.innerHTML = '<option value="" selected></option>';
                        data.forEach(function(product) {
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