<?php $__env->startSection('body'); ?>
    <div class="flex justify-between">
        <h3 class="text-gray-700 text-3xl font-medium">Şassilər</h3>
        <a href="<?php echo e(route('highways.create')); ?>"
            class="flex items-center justify-center px-4 py-2 bg-indigo-500 text-white rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14"
                height="14">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Yenisini yarat
        </a>
    </div>
    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div>
                <form action="<?php echo e(route('highways.index')); ?>" method="get">
                    <div class="grid grid-cols-2 mt-4 gap-3 mb-4">
                        <div>
                            <label class="text-gray-700" for="warehouse">Şassi nömrəsi</label>
                            <input value="<?php echo e($highway_code ? $highway_code : ''); ?>" name="highway_code" id="highway_code" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="text">
                        </div>
                        <div>
                            <label class="text-gray-700" for="from_warehouse">Anbardan</label>
                            <select id="from_warehouse" name="from_warehouse_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option selected value="">Anbar seçin</option>
                                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($warehouse->id); ?>"><?php echo e($warehouse->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-700" for="product">Məhsul</label>
                            <select id="product" name="product_id"
                                class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="" selected>Məhsul seçin</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option <?php echo e($product_id && $product_id == $product->id ? 'selected' : ''); ?>

                                        value="<?php echo e($product->id); ?>"><?php echo e($product->name); ?> <?php echo e($product->code ? '- ' . $product->code : ''); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Nömrəsi
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Məhsul
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Sayı
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Anbardan
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                PDF faylı
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tarix
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        <?php $__currentLoopData = $highways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $highway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($highway->code); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($highway->product_name); ?> <?php echo e($highway->product_code ? '- ' . $highway->product_code : ''); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($highway->quantity); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($highway->warehouse_name); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php if($highway->pdf_file): ?>
                                        <a class="text-indigo-400 underline" href="<?php echo e(asset('storage/' . $highway->pdf_file)); ?>" target="_blank">Göstər</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($highway->entry_date); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="flex items-center justify-center my-2">
                    <?php echo $__env->make('components.pagination', ['paginator' => $highways], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/pages/highway/index.blade.php ENDPATH**/ ?>