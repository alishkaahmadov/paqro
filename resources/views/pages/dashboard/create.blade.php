@extends('layouts.master')

@section('body')
    <h3 class="text-gray-700 text-3xl font-medium">Yeni məhsul əlavə et</h3>

    <div class="flex flex-col mt-8">
        <form action="{{ route('dashboard.store') }}" method="post">
            @csrf
            <div id="mainDiv" class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="warehouse">Anbar</label>
                    <input list="warehouses" id="warehouse" name="warehouse_name"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        type="text" autocomplete="off">
                    <input type="hidden" id="warehouse_id" name="warehouse_id">
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
                        type="text" autocomplete="off">
                    <input type="hidden" id="company_id" name="company_id">
                    <datalist id="companies">
                        @foreach ($companies as $company)
                            <option data-id="{{ $company->id }}" value="{{ $company->name }}"></option>
                        @endforeach
                    </datalist>
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
                        <label class="text-gray-700" for="product">Məhsul</label>
                        <input list="products" id="product" name="products[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
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
                        <input id="product_code" name="product_codes[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="quantity">Sayı</label>
                        <input name="quantities[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="category">Kateqoriya</label>
                        <input list="categories" id="category" name="categories[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
                        <datalist id="categories">
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="entry_date">Giriş tarixi</label>
                        <input name="entry_dates[]" data-datetime-local="true"
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

        function addNewElements(number){
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
                        <label class="text-gray-700" for="product">Məhsul</label>
                        <input list="products" id="product" name="products[]"
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
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
                        <input id="product_code" name="product_codes[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="quantity">Sayı</label>
                        <input name="quantities[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="category">Kateqoriya</label>
                        <input list="categories" id="category" name="categories[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
                        <datalist id="categories">
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="entry_date">Giriş tarixi</label>
                        <input name="entry_dates[]" data-datetime-local="true"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="datetime-local"  value="${formattedDateTime}">
                    </div>
                </div>
            `;

            const mainDiv = document.getElementById('mainDiv');

            for (let i = 0; i < number; i++) {
                const newSet = document.createElement('div');
                newSet.className = 'space-y-4';
                newSet.innerHTML = newElementsHTML;
                mainDiv.appendChild(newSet);
            }

            const productInputs = [...document.querySelectorAll('.product-input')];

            productInputs.forEach(productInput => {
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
            })
        }

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

            // Define the HTML for the elements to be added
            const newElementsHTML = `
                <div class="relative flex justify-between flex-col md:flex-row mt-2 pt-8">
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="product">Məhsul</label>
                        <input list="products" id="product" name="products[]"
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
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
                        <input id="product_code" name="product_codes[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="quantity">Sayı</label>
                        <input name="quantities[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="number">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="category">Kateqoriya</label>
                        <input list="categories" id="category" name="categories[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
                        <datalist id="categories">
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="entry_date">Giriş tarixi</label>
                        <input name="entry_dates[]" data-datetime-local="true"
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
            }

            // Set the inner HTML of the container div
            newSet.innerHTML = newElementsHTML;

            // Append the new set of elements to the main container
            document.getElementById('mainDiv').appendChild(newSet);

            const productInputs = [...document.querySelectorAll('.product-input')];

            productInputs.forEach(productInput => {
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
            })

        });

        addNewElements(30)
    </script>
@endsection
