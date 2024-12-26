<?php $__env->startSection('body'); ?>
    <h3 class="text-gray-700 text-3xl font-medium">Visual Cədvəl</h3>
        
    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm font-semibold text-gray-700 uppercase">Anbar</th>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-center text-sm font-semibold text-gray-700 uppercase"><?php echo e($product->name); ?> <?php echo e($product->code ? '- ' . $product->code : ''); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                                <td class="px-6 py-4 border-b border-gray-200 text-sm leading-5 font-medium text-gray-900"><?php echo e($warehouse->name); ?></td>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $quantity = $warehouse->product_entries->where('product_id', $product->id)->first()->quantity ?? 0;
                                    ?>
                                    <td class="px-6 py-4 border-b border-gray-200 text-sm leading-5 text-center <?php echo e($quantity > 0 ? 'text-green-600 font-bold' : 'text-red-600 font-semibold'); ?>">
                                        <?php echo e($quantity); ?> ədəd
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/pages/visual_table.blade.php ENDPATH**/ ?>