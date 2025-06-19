@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Yeni Şassi daxil et</h3>

    <div class="flex flex-col mt-8">
        <form action="{{ route('highways.store') }}" method="post" id="myForm" enctype="multipart/form-data" onsubmit="validateForm(event)">
            @csrf
            <div id="mainDiv" class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="warehouse_id">Anbar</label>
                    <select id="warehouse_id" name="warehouse_id"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option selected>Anbar seçin</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-gray-700" for="code">Şassi nömrəsi</label>
                    <input name="code"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        type="text">
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
                        <select id="products" name="products[]"
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            @isset($products)
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700">Sayı</label>
                        <input name="quantities[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number" step="any">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700">Ölçü vahidi</label>
                        <input name="notes[]" id="measure"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" placeholder="litr/ədəd">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700">Moto saat</label>
                        <input name="moto_saats[]" id="moto_saat"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="pdf">PDF faylı</label>
                        <input name="pdfs[]" id="pdfs"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="file" accept="application/pdf">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="date">Tarix</label>
                        <input name="dates[]" data-datetime-local="true"
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
        let html = '';
        let addingLoop = 0;
        let allProductsData = [];
        $('#products').select2();

        function addNewElements(number = 30) {
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

            // Define the HTML for the elements to be added
            const newElementsHTML = `
            <div class="relative flex justify-between flex-col md:flex-row mt-2 pt-8">
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="products">Məhsul</label>
                        <select id="products" name="products[]"
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            ${html}
                        </select>
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="quantity">Sayı</label>
                        <input name="quantities[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number" step="any">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700">Ölçü vahidi</label>
                        <input name="notes[]" id="measure"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" placeholder="litr/ədəd">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700">Moto saat</label>
                        <input name="moto_saats[]" id="moto_saat"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="pdf">PDF faylı</label>
                        <input name="pdfs[]" id="pdfs"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="file" accept="application/pdf">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="date">Tarix</label>
                        <input name="dates[]" data-datetime-local="true"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="datetime-local" value="${formattedDateTime}">
                    </div>
                </div>
            `;
           
            const mainDiv = document.getElementById('mainDiv');

            for (let i = 0; i < number; i++) {
                const newSet = document.createElement('div');
                newSet.className = 'space-y-4';
                newSet.innerHTML = newElementsHTML;
                mainDiv.appendChild(newSet);
                newSet.children[0].children[0].children[1].id = `alishka-${i + addingLoop}`
                $(`#alishka-${i + addingLoop}`).select2();
            }
        }

        function validateForm(event) {
            event.preventDefault();
            if(![...document.querySelectorAll('input[name="pdfs[]"]')][0].value){
                if(!confirm("PDF olmadan yükləməkdən əminsiniz?")) return;
            }

            const products = [...document.querySelectorAll('select[name="products[]"]')]
            const quantites = [...document.querySelectorAll('input[name="quantities[]"]')]
            let isValid = true;
            for (let i = 0; i < products.length; i++) {
                const element = products[i];
                if(element.value && quantites[i].value){
                    const selectedProductQunatity = allProductsData.find(item => item.id == element.value)?.quantity;
                    if(selectedProductQunatity < quantites[i].value){
                        quantites[i].classList.add('quantity_error');
                        isValid = false;
                        break;
                    }else{
                        if(quantites[i].classList.contains('quantity_error')) quantites[i].classList.remove('quantity_error');
                    }
                }
            }
            if(isValid) document.getElementById('myForm').submit();
        }
        
        document.getElementById('addMore').addEventListener('click', function(event) {
            event.preventDefault();
            addingLoop += 30;
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

            // Define the HTML for the elements to be added
            const newElementsHTML = `
            <div class="relative flex justify-between flex-col md:flex-row mt-2 pt-8">
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="products">Məhsul</label>
                        <select id="products" name="products[]"
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            ${html}
                        </select>
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="quantity">Sayı</label>
                        <input name="quantities[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number" step="any">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700">Ölçü vahidi</label>
                        <input name="notes[]" id="measure"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" placeholder="litr/ədəd">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700">Moto saat</label>
                        <input name="moto_saats[]" id="moto_saat"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="pdf">PDF faylı</label>
                        <input name="pdfs[]" id="pdfs"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="file" accept="application/pdf">
                    </div>

                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="date">Tarix</label>
                        <input name="dates[]" data-datetime-local="true"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="datetime-local" value="${formattedDateTime}">
                    </div>
                </div>
            `;

            const mainDiv = document.getElementById('mainDiv');

            for (let i = 0; i < 30; i++) {
                const newSet = document.createElement('div');
                newSet.className = 'space-y-4';
                newSet.innerHTML = newElementsHTML;
                mainDiv.appendChild(newSet);
                newSet.children[0].children[0].children[1].id = `alishka-${i + addingLoop}`
                $(`#alishka-${i + addingLoop}`).select2();
            }
        });

        document.getElementById('warehouse_id').addEventListener('change', function() {
            
            var warehouseId = this.value;

            if (warehouseId) {
                fetch(`/get-products/${warehouseId}`)
                    .then(response => response.json())
                    .then(data => {

                        allProductsData = [...data];
                        var productsSelect = [...document.querySelectorAll('.product-input')];
                        productsSelect.forEach(products => {
                            products.innerHTML = '<option value="" selected></option>';
                            html = '<option value="" selected></option>';
                            data.forEach(function(product) {
                                if(product.quantity){
                                    html += `<option value="${product.id}">${product.product_name} ${product.product_code ? `- ${product.product_code}` : ''} - ${product.category_name} (${product.quantity})</option>`
                                    var option = document.createElement('option');
                                    option.value = product.id;
                                    option.textContent = `${product.product_name} ${product.product_code ? `- ${product.product_code}` : ''} - ${product.category_name} (${product.quantity})`;
                                    products.appendChild(option);
                                }
                            });
                        })
                    })
                    .catch(error => console.error('Error fetching products:', error));
            } else {
                document.getElementById('products').innerHTML = '<option selected value="">Məhsul seçin</option>';
            }
        });
        addNewElements(30);
    </script>
@endsection
