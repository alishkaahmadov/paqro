<?php $__env->startSection('body'); ?>
    <h3 class="text-gray-700 text-3xl font-medium">Ümumi</h3>
    <div class="flex justify-between w-full mt-4">
        <div>
            <a href="<?php echo e(route('dashboard.index')); ?>"
                class="w-full mr-2 px-4 py-2 rounded-md focus:outline-none <?php echo e(request()->routeIs('dashboard.index') ? 'bg-gradient-to-r from-indigo-500 to-indigo-700 text-white shadow-lg border-b-4 border-indigo-800' : 'bg-indigo-500 text-white hover:bg-indigo-600 hover:shadow-md'); ?>">
                Qalıq
            </a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\WarehouseLog::class)): ?>
                <a href="<?php echo e(route('dashboard.create')); ?>"
                    class="w-full px-4 py-2 bg-green-500 text-white rounded-md mr-2">
                    Giriş daxil et
                </a>
                <a href="<?php echo e(route('dashboard.transferPage')); ?>"
                    class="w-full px-4 py-2 bg-orange-500 text-white rounded-md">
                    Çıxış daxil et
                </a>
            <?php endif; ?>
        </div>
        <div>
            <a href="<?php echo e(route('dashboard.entries')); ?>"
                class="w-full mr-2 px-4 py-2 rounded-md focus:outline-none <?php echo e(request()->routeIs('dashboard.entries') ? 'bg-gradient-to-r from-gray-500 to-gray-700 text-white shadow-lg border-b-4 border-gray-800' : 'bg-gray-500 text-white hover:bg-gray-600 hover:shadow-md'); ?>">
                Girişlər
            </a>
            <a href="<?php echo e(route('dashboard.exits')); ?>"
                class="w-full mr-2 px-4 py-2 rounded-md focus:outline-none <?php echo e(request()->routeIs('dashboard.exits') ? 'bg-gradient-to-r from-gray-500 to-gray-700 text-white shadow-lg border-b-4 border-gray-800' : 'bg-gray-500 text-white hover:bg-gray-600 hover:shadow-md'); ?>">
                Çıxışlar
            </a>
            <a href="<?php echo e(route('dashboard.overall')); ?>"
                class="w-full px-4 py-2 rounded-md focus:outline-none <?php echo e(request()->routeIs('dashboard.overall') ? 'bg-gradient-to-r from-gray-500 to-gray-700 text-white shadow-lg border-b-4 border-gray-800' : 'bg-gray-500 text-white hover:bg-gray-600 hover:shadow-md'); ?>">
                Ümumi
            </a>
        </div>
    </div>
    <div class="flex flex-col mt-3">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div>
                <form action="<?php echo e(route('dashboard.overall')); ?>" method="get">
                    <div class="grid grid-cols-3 mt-4 gap-3 mb-4">
                        <div>
                            <label class="text-gray-700" for="warehouse">Anbar</label>
                            <select id="warehouse" name="warehouse_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        <?php echo e($warehouse_id && $warehouse_id == $warehouse->id ? 'selected' : ($warehouse->id == 1 ? 'selected' : '')); ?>

                                        value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="warehouse">Anbardan</label>
                            <select id="warehouse" name="from_warehouse_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Anbar seçin</option>
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php echo e($from_warehouse_id && $from_warehouse_id == $warehouse->id ? 'selected' : ''); ?>

                                        value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="warehouse">Anbara</label>
                            <select id="warehouse" name="to_warehouse_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Anbar seçin</option>
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php echo e($to_warehouse_id && $to_warehouse_id == $warehouse->id ? 'selected' : ''); ?>

                                        value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="product">Məhsul</label>
                            <select id="product" name="product_ids[]" multiple
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($product->id); ?>"><?php echo e($product->name); ?> <?php echo e($product->code ? '- ' . $product->code : ''); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="company">Şirkət</label>
                            <select id="company" name="company_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Şirkət seçin</option>
                                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php echo e($company_id && $company_id == $company->id ? 'selected' : ''); ?>

                                        value="<?php echo e($company->id); ?>"><?php echo e($company->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="category">Kateqoriya</label>
                            <select id="category" name="category_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Kateqoriya seçin</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php echo e($category_id && $category_id == $category->id ? 'selected' : ''); ?>

                                        value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="highway_code">Şassi nömrəsi</label>
                            <input value="<?php echo e($highway_code ? $highway_code : ''); ?>" name="highway_code" id="highway_code" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="text">
                        </div>
                        
                        <div>
                            <label class="text-gray-700" for="start_date">Tarixdən</label>
                            <input value="<?php echo e($start_date ? $start_date : ''); ?>" name="start_date" id="start_date" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="datetime-local">
                        </div>
                        <div>
                            <label class="text-gray-700" for="end_date">Tarixədək</label>
                            <input value="<?php echo e($end_date ? $end_date : ''); ?>" name="end_date" id="end_date" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="datetime-local">
                        </div>
                        <div>
                            <label class="text-gray-700" for="type">Tip</label>
                            <select id="type" name="type_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Tip seçin</option>
                                <option <?php echo e($type_id && $type_id == 1 ? 'selected' : ''); ?> value="1">Giriş</option>
                                <option <?php echo e($type_id && $type_id == 2 ? 'selected' : ''); ?> value="2">Çıxış</option>
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
                                №
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Kod
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Məhsul
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Qədərki qalıq
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Sayı
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Qalıq
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Şirkət
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Anbardan
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Anbara
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Kateqoriya
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Şassi nömrəsi
                            </th>
                            
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tip
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tarix
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        <?php $__currentLoopData = $productEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($loop->index + 1); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->product_code ?? ''); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->product_name); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->residual ?? 0); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->quantity); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->residual + ($warehouseId == $product->to_warehouse_id ? $product->quantity : -$product->quantity)); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->company_name ?? "-"); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->from_warehouse ? $product->from_warehouse : '-'); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->to_warehouse ? $product->to_warehouse : '-'); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->subcategory_name); ?>

                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->highway_code ? $product->highway_code : '-'); ?>

                                </td>
                                
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <span class="<?php echo e($warehouseId == $product->to_warehouse_id ? 'process-type-entry' : 'process-type-exit'); ?>"><?php echo e($warehouseId == $product->to_warehouse_id ? 'Giriş' : 'Çıxış'); ?></span>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($product->entry_date); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="grid my-2">
                    <form class="flex w-1/2 justify-self-end" id="exportForm">
                        <input type="hidden" name="export_type" value="all" id="export_type">
                        <button
                            class="mr-2 w-full px-4 py-2 bg-green-500 border-2 border-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400"
                            type="button" onclick="printToExcel(event)">Çap et (Excel)</button>
                        <button
                            class="mr-2 w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-md shadow-lg hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            type="button" onclick="setExportType(event, 'current')">Səhifəni çap et (PDF)</button>
                        <button
                            class="w-full px-4 py-2 bg-red-500 border-2 border-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400"
                            type="button" onclick="setExportType(event, 'all')">Çap et (PDF)</button>
                    </form>
                </div>
                <div class="flex items-center justify-center my-2">
                    <?php echo $__env->make('components.pagination', ['paginator' => $productEntries], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            $('#product').select2();
            const selectedProductIds = <?php echo json_encode($product_ids, 15, 512) ?>;
            $('#product').val(selectedProductIds).trigger('change');
        });

        function setExportType(event, type) {
            event.preventDefault(); // Prevent the default form submission

            // Set the export type in the hidden input
            document.getElementById('export_type').value = type;

            // Construct the URL with query parameters
            const params = new URLSearchParams(window.location.search);
            params.set('export_type', type); // Update or add the export_type parameter

            // Create the full URL with parameters
            const actionUrl = `<?php echo e(route('export.overallProducts')); ?>?${params.toString()}`;

            // Redirect to the constructed URL to trigger the form submission
            window.location.href = actionUrl;
        }
        function printToExcel(event) {
            event.preventDefault();
            const params = new URLSearchParams(window.location.search);
            const actionUrl = `<?php echo e(route('export.overallExcel')); ?>?${params.toString()}`;
            window.location.href = actionUrl;
        }
    </script>
<?php $__env->stopSection(); ?>

<?php if(session('success')): ?>
    <?php $__env->startSection('script'); ?>
        <script>
            toastr.options = {
                "progressBar": true,
                "closeButton": true
            }
            toastr.success("<?php echo e(session('success')); ?>", "Uğurlu!");
        </script>
    <?php $__env->stopSection(); ?>
<?php endif; ?>
<?php if(session('error')): ?>
    <?php $__env->startSection('script'); ?>
        <script>
            toastr.options = {
                "progressBar": true,
                "closeButton": true
            }
            toastr.error("<?php echo e(session('error')); ?>", "Xəta!");
        </script>
    <?php $__env->stopSection(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/pages/dashboard/overall.blade.php ENDPATH**/ ?>