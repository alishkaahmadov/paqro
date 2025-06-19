<?php $__env->startSection('body'); ?>
    <h3 class="text-gray-700 text-3xl font-medium">Yeni transfer əlavə et</h3>

    <div class="flex flex-col mt-8">
        <form action="<?php echo e(route('dashboard.transfer')); ?>" method="post" id="myForm" onsubmit="validateForm(event)">
            <?php echo csrf_field(); ?>
            <div id="mainDiv" class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="from_warehouse">Anbardan</label>
                    <select id="from_warehouse" name="from_warehouse"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option <?php echo e($mainWarehouseId && $mainWarehouseId == $warehouse->id ? 'selected' : ($warehouse->id == 1 ? 'selected' : '')); ?> value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="text-gray-700" for="to_warehouse">Anbara</label>
                    <select id="to_warehouse" name="to_warehouse"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option disabled selected>Anbar seçin</option>
                        <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="text-gray-700" for="to_whom">Kimə</label>
                    <input name="to_whom" placeholder="Bakı filialı Anbardarı Babayev Elman İlham oğlu"
                        class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        type="text">
                </div>

                <div class="flex justify-between mt-2">
                    <div class="md:w-1/2">
                        <label class="text-gray-700" for="pdf_date">Tarix (PDF üçün)</label>
                        <input name="pdf_date" data-datetime-local="true"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="datetime-local">
                    </div>
    
                    <div class="md:w-1/2">
                        <label class="text-gray-700" for="pdf_doc_number">Tələbnamə nömrəsi (PDF üçün)</label>
                        <input name="pdf_doc_number"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="text">
                    </div>
                </div>

                <div class="flex justify-end mt-2">
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
                        <label class="text-gray-700" for="products">Məhsul</label>
                        <select id="products" name="products[]"
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <?php if(isset($products)): ?>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($product->id); ?>"><?php echo e($product->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
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
                        <label class="text-gray-700" for="transfer_date">Transfer tarixi</label>
                        <input name="transfer_dates[]" data-datetime-local="true"
                            class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            type="datetime-local">
                    </div>
                </div>

            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" name="action" value="save"
                    class="px-4 py-2 mr-2 bg-gray-800 text-gray-200 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Yarat</button>
                <button type="submit" name="action" value="print"
                    class="px-4 py-2 bg-gray-800 text-gray-200 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Yarat (Çap et)</button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        let allProductsData = [];

        const form = document.getElementById('myForm');
        let clickedButtonValue = null;

        form.querySelectorAll('button[type="submit"]').forEach(button => {
            button.addEventListener('click', (event) => {
                submitFormWithButton(event.target.name, event.target.value);
            });
        });

        function validateForm(event) {
            event.preventDefault();

            const products = [...document.querySelectorAll('select[name="products[]"]')]
            const quantites = [...document.querySelectorAll('input[name="quantities[]"]')]
            let isValid = true;
            for (let i = 0; i < products.length; i++) {
                const element = products[i];
                if(element.value && quantites[i].value){
                    const selectedProductQunatity = allProductsData.find(item => item.id == element.value)?.quantity;
                    if(parseFloat(selectedProductQunatity) < parseFloat(quantites[i].value)){
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

        function submitFormWithButton(name, value) {

            // Create a hidden input to mimic the button's name and value
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = name;
            hiddenInput.value = value;

            // Append the hidden input to the form
            form.appendChild(hiddenInput);
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

        let addingLoop = 0;
        $('#products').select2();
        let html = '';
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
                        <select name="products[]"
                            class="product-input mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
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
                        <input name="notes[]"
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

        function setDefaultWarehouse(){
            const mainWarehouseId = <?php echo e($mainWarehouseId); ?>


            if(mainWarehouseId){
                fetch(`/get-products/${mainWarehouseId}`)
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
            }else{
                document.getElementById('products').innerHTML = '';
                html = '';
            }
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
                        <select name="products[]"
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
                        <input name="notes[]"
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

        document.getElementById('from_warehouse').addEventListener('change', function() {
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
                                html += `<option value="${product.id}">${product.product_name} ${product.product_code ? `- ${product.product_code}` : ''} - ${product.category_name} (${product.quantity})</option>`
                                var option = document.createElement('option');
                                option.value = product.id;
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

        addNewElements(30);
        setDefaultWarehouse();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\Desktop\Alishka Projects\paqro\resources\views/pages/dashboard/transfer.blade.php ENDPATH**/ ?>