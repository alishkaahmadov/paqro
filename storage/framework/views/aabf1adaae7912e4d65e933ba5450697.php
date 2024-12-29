

<?php $__env->startSection('body'); ?>
    <div class="flex justify-between">
        <h3 class="text-gray-700 text-3xl font-medium">Anbarlar</h3>
        <a href="<?php echo e(route('warehouses.create')); ?>"
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
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full text-center">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Adı
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Anbardar
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Əsas anbar
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Yaranma tarixi
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tənzimlə / Sil
                            </th>
                        </tr>
                    </thead>
    
                    <tbody class="bg-white">
                        <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($warehouse->name); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($warehouse->warehouseman); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($warehouse->is_main ? "Bəli" : "Xeyr"); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-bold">
                                    <?php echo e($warehouse->created_at); ?>

                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5">
                                    <a href="<?php echo e(route('warehouses.edit', $warehouse->id)); ?>"
                                        class="text-blue-500 hover:text-blue-700 mr-2">
                                        Düzəliş et
                                     </a>
                                     <form action="<?php echo e(route('warehouses.destroy', $warehouse->id)); ?>" method="POST" class="inline-block">
                                         <?php echo csrf_field(); ?>
                                         <?php echo method_field('DELETE'); ?>
                                         <button type="submit" onclick="return confirm('Silmək istədiyinizdən əminsiniz?')" 
                                                 class="text-red-500 hover:text-red-700">
                                             Sil
                                         </button>
                                     </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="flex items-center justify-center my-2">
                    <?php echo $__env->make('components.pagination', ['paginator' => $warehouses], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\Desktop\Alishka Projects\paqro\resources\views/pages/warehouse/index.blade.php ENDPATH**/ ?>