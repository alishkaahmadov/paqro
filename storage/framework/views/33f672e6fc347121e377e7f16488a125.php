<?php $__env->startSection('body'); ?>
    <h3 class="text-gray-700 text-3xl font-medium">Yeni şirkət yarat</h3>

    <div class="flex flex-col mt-8">
        <form action="<?php echo e(route('companies.store')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 mt-4">
                <div>
                    <label class="text-gray-700" for="name">Şirkətin adı</label>
                    <input name="name" class="mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" type="text">
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-gray-200 rounded-md hover:bg-gray-700 focus:outline-none focus:bg-gray-700">Yarat</button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/pages/company/create.blade.php ENDPATH**/ ?>