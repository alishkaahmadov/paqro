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
                        type="text" autocomplete="off" value="{{$mainWarehouse->name ?? ""}}">
                    <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{$mainWarehouse->id ?? ""}}">
                    <datalist id="warehouses">
                        @foreach ($warehouses as $warehouse)
                            <option data-id="{{ $warehouse->id }}" value="{{ $warehouse->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="text-gray-700" for="company">Şirkət</label>
                    <input list="companies" id="company" name="company_name" required
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        type="text" autocomplete="off">
                    <input type="hidden" id="company_id" name="company_id">
                    <datalist id="companies">
                        @foreach ($companies as $company)
                            <option data-id="{{ $company->id }}" value="{{ $company->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="flex justify-end mt-2">
                    <button id="identifyCategory" class="px-4 py-2 bg-indigo-500 text-white rounded-md mr-2">Kateqoriyanı eyniləşdir</button>
                    <button id="identifyMeasure" class="px-4 py-2 bg-indigo-500 text-white rounded-md mr-2">Ölçü vahidini eyniləşdir</button>
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
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
                        <datalist id="products" class="product-data-list">
                            {{-- @foreach ($products as $product)
                                <option data-code="{{ $product->code }}" value="{{ $product->name . '---' . $product->code }}">
                                    {{ $product->name }} {{ $product->code ? '- ' . $product->code : '' }}
                                </option>
                            @endforeach --}}
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
                            type="number" step="any">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700">Ölçü vahidi</label>
                        <input name="notes[]" id="measure"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" placeholder="litr/ədəd">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700">Rəf</label>
                        <input name="shelfs[]" id="shelf"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text">
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
        const mainWarehouseId = {{$mainWarehouse->id}}
        function debounce(func, delay) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }
        
        const identifyMeasure = document.getElementById('identifyMeasure');
        identifyMeasure.addEventListener('click', function(event){
            event.preventDefault();
            const measureItem = document.getElementById('measure');
            if(this.innerHTML === "Ölçü vahidini eyniləşdir"){
                if(measureItem.value){
                    const measureItems = [...document.querySelectorAll('input[name="notes[]"]')];
                    measureItems.forEach(item => {
                        item.value = measureItem.value;
                    });
                    this.innerHTML = "Eyniləşdirməni sil";
                }
            }else{
                const measureItems = [...document.querySelectorAll('input[name="notes[]"]')];
                    measureItems.forEach(item => {
                        item.value = '';
                    });
                this.innerHTML = "Ölçü vahidini eyniləşdir";
            }
        })

        const identifyCategory = document.getElementById('identifyCategory');
        identifyCategory.addEventListener('click', function(event){
            event.preventDefault();
            const categoryItem = document.getElementById('category');
            if(this.innerHTML === "Kateqoriyanı eyniləşdir"){
                if(categoryItem.value){
                    const categoryItems = [...document.querySelectorAll('input[name="categories[]"]')];
                    categoryItems.forEach(item => {
                        item.value = categoryItem.value;
                    });
                    this.innerHTML = "Eyniləşdirməni sil";
                }
            }else{
                const categoryItems = [...document.querySelectorAll('input[name="categories[]"]')];
                    categoryItems.forEach(item => {
                        item.value = '';
                    });
                this.innerHTML = "Kateqoriyanı eyniləşdir";
            }
        })

        document.getElementById('warehouse').addEventListener('input', async function() {
            // Get the list of options from the datalist
            const options = document.getElementById('warehouses').options;
            const warehouseInput = document.getElementById('warehouse');
            const hiddenWarehouseId = document.getElementById('warehouse_id');

            // Loop through options to find the selected warehouse name and get its ID
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === warehouseInput.value) {
                    // Set the hidden input field with the corresponding warehouse ID
                    hiddenWarehouseId.value = options[i].getAttribute('data-id');
                    debouncedSetProductData(options[i].getAttribute('data-id'));
                    break;
                } else {
                    // Clear the hidden input if no match is found
                    hiddenWarehouseId.value = '';
                    // Set default quantity and category for products
                    debouncedSetAllProductData()
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
                            type="number" step="any">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700">Ölçü vahidi</label>
                        <input name="notes[]" id="measure"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" placeholder="litr/ədəd">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700">Rəf</label>
                        <input name="shelfs[]" id="shelf"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="category">Kateqoriya</label>
                        <input list="categories" id="category" name="categories[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
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
            const newSet2 = document.createElement('div');
            newSet2.innerHTML = newElementsHTML;

            // Append the new set of elements to the main container
            document.getElementById('mainDiv').appendChild(newSet2);

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
                            type="number" step="any">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700">Ölçü vahidi</label>
                        <input name="notes[]" id="measure"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" placeholder="litr/ədəd">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700">Rəf</label>
                        <input name="shelfs[]" id="shelf"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text">
                    </div>
                    <div class="md:w-2/5">
                        <label class="text-gray-700" for="category">Kateqoriya</label>
                        <input list="categories" id="category" name="categories[]"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text" autocomplete="off">
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
        }
        
        addNewElements(30)
        // Set Data List options for products
        const productDataLists = [...document.querySelectorAll('.product-data-list')];
        let productOptions = null;
        
        async function setProductData(id){
            let totalHtml = '';
            const warehouseData = await getWarehouseData(id);

            warehouseData.forEach(data => {
                totalHtml += `
                    <option data-code="${data.product_code}" value="${data.product_name + '---' + data.product_code}">
                        ${data.product_name} ${data.product_code ? '- ' + data.product_code : ''} ${data.category_name} (${data.quantity})
                    </option>
                `
            })

            productDataLists.forEach(dataList => {
                dataList.innerHTML = totalHtml;
            })
            productOptions = [...document.getElementById('products').options];
        }

        function setAllProductData(){
            productDataLists.forEach(dataList => {
                dataList.innerHTML = `
                    @foreach ($products as $product)
                        <option data-code="{{ $product->code }}" value="{{ $product->name . '---' . $product->code }}">
                            {{ $product->name }} {{ $product->code ? '- ' . $product->code : '' }}
                        </option>
                    @endforeach
                `
            })
            productOptions = [...document.getElementById('products').options];
        }

        const debouncedSetProductData = debounce(setProductData, 1500);
        const debouncedSetAllProductData = debounce(setAllProductData, 1500);

        async function getWarehouseData(id){
            let result = null;
            if(id){
                await fetch(`/get-products/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        result = data
                    })
                    .catch(error => console.error('Error fetching products:', error));
            }
            return result;
        }
        
        async function setDatalist(){
            await setProductData(mainWarehouseId);
            // Set auto code functionality
            productOptions = [...document.getElementById('products').options];
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
        
        setDatalist()
    </script>
@endsection
