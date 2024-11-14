@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Yeni transfer əlavə et</h3>

    <div class="flex flex-col mt-8">
        <form action="{{ route('dashboard.transfer') }}" method="post">
            @csrf
            <div id="mainDiv" class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="from_warehouse">Anbardan</label>
                    <select id="from_warehouse" name="from_warehouse"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach ($warehouses as $warehouse)
                            <option {{ $mainWarehouseId && $mainWarehouseId == $warehouse->id ? 'selected' : ($warehouse->id == 1 ? 'selected' : '') }} value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-gray-700" for="to_warehouse">Anbara</label>
                    <select id="to_warehouse" name="to_warehouse"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option disabled selected>Anbar seçin</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="relative flex justify-between flex-col md:flex-row mt-2 pt-8">
                    <button id="addMore"
                        class="absolute top-0 right-0 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full flex items-center justify-center shadow-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H5a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="products">Məhsul</label>
                        <select id="products" name="products[]" required
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            @isset($products)
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
    
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="quantity">Sayı</label>
                        <input name="quantities[]" required
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700">Qeyd</label>
                        <input name="notes[]" required
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" placeholder="litr/ədəd">
                    </div>
    
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="transfer_date">Transfer tarixi</label>
                        <input name="transfer_dates[]" data-datetime-local="true"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="datetime-local">
                    </div>
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
        document.getElementById('addMore').addEventListener('click', function(event) {
            event.preventDefault();
            var now = new Date().toLocaleString("en-US", {
                timeZone: "Asia/Baku"
            });
            var timezoneDate = new Date(now);
            // Format the date to YYYY-MM-DDTHH:MM
            var formattedDateTime = timezoneDate.getFullYear() + '-' +
                String(timezoneDate.getMonth() + 1).padStart(2, '0') + '-' +
                String(timezoneDate.getDate()).padStart(2, '0') + 'T' +
                String(timezoneDate.getHours()).padStart(2, '0') + ':' +
                String(timezoneDate.getMinutes()).padStart(2, '0');
            // Create a new container div for the set of elements
            const newSet = document.createElement('div');
            newSet.className = 'space-y-4';

            // Define the HTML for the elements to be added
            const newElementsHTML = `
            <div class="relative flex justify-between flex-col md:flex-row mt-2 pt-8">
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="products">Məhsul</label>
                        <select id="products" name="products[]" required
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            ${html}
                        </select>
                    </div>
    
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="quantity">Sayı</label>
                        <input name="quantities[]" required
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700">Qeyd</label>
                        <input name="notes[]" required
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" placeholder="litr/ədəd">
                    </div>
    
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="transfer_date">Transfer tarixi</label>
                        <input name="transfer_dates[]" data-datetime-local="true"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="datetime-local" value="${formattedDateTime}">
                    </div>
                </div>
            `;

            // Set the inner HTML of the container div
            newSet.innerHTML = newElementsHTML;

            // Append the new set of elements to the main container
            document.getElementById('mainDiv').appendChild(newSet);
        });

        let html = '<option selected value="">Məhsul seçin</option>'

        document.getElementById('from_warehouse').addEventListener('change', function() {
            var warehouseId = this.value;

            if (warehouseId) {
                fetch(`/get-products/${warehouseId}`)
                    .then(response => response.json())
                    .then(data => {
                        var productsSelect = [...document.querySelectorAll('.product-input')];
                        productsSelect.forEach(products => {
                            products.innerHTML = '<option selected value="">Məhsul seçin</option>'; // Reset options
                            html = '<option selected value="">Məhsul seçin</option>';
                            data.forEach(function(product) {
                                html += `<option value="${product.product_id}">${product.product_name} ${product.product_code ? `- ${product.product_code}` : ''} - ${product.category_name} (${product.quantity})</option>`
                                var option = document.createElement('option');
                                option.value = product.product_id;
                                option.textContent = `${product.product_name} ${product.product_code ? `- ${product.product_code}` : ''} - ${product.category_name} (${product.quantity})`;
                                products.appendChild(option);
                            });
                        })

                    })
                    .catch(error => console.error('Error fetching products:', error));
            } else {
                document.getElementById('products').innerHTML = '<option selected value="">Məhsul seçin</option>';
                html = '<option selected value="">Məhsul seçin</option>';
            }
        });
    </script>
@endsection
