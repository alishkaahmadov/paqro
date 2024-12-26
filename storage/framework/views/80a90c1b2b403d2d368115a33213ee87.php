<?php $__env->startSection('body'); ?>
<div class="container mx-auto">
    <h2 class="text-xl font-bold mb-4">Anbar düzəlişi</h2>

    <form action="<?php echo e(route('warehouses.update', $warehouse->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Adı</label>
            <input type="text" id="name" name="name" value="<?php echo e(old('name', $warehouse->name)); ?>"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mb-4">
            <label for="warehouseman" class="block text-sm font-medium text-gray-700">Anbardar</label>
            <input type="text" id="warehouseman" name="warehouseman" value="<?php echo e(old('warehouseman', $warehouse->warehouseman)); ?>"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <?php $__errorArgs = ['warehouseman'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mb-4">
            <label for="is_main" class="block text-sm font-medium text-gray-700">Əsas anbar</label>
            <select id="is_main" name="is_main"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="1" <?php echo e($warehouse->is_main ? 'selected' : ''); ?>>Bəli</option>
                <option value="0" <?php echo e(!$warehouse->is_main ? 'selected' : ''); ?>>Xeyr</option>
            </select>
            <?php $__errorArgs = ['is_main'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mt-6">
            <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-bold rounded hover:bg-blue-700">
                Dəyiş
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/pages/warehouse/edit.blade.php ENDPATH**/ ?>