<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anbar</title>
</head>
<body>
    <script>
        window.onload = function() {
            const fileName = "<?php echo e($fileName); ?>";
            const downloadUrl = "<?php echo e(route('download.pdf')); ?>?file=" + encodeURIComponent(fileName);

            window.location.href = downloadUrl;

            setTimeout(function() {
                window.location.href = "<?php echo e(route('dashboard.index')); ?>";
            }, 1500);
        };
    </script>
</body>
</html>
<?php /**PATH C:\Users\User\Desktop\Alishka Projects\paqro\resources\views/pages/pdf/download.blade.php ENDPATH**/ ?>