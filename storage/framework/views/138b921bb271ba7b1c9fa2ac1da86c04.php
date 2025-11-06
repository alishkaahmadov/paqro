<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="referrer" content="always">
    <meta name="description" content="P-Aqro">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"
        integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css"
        integrity="sha512-6S2HWzVFxruDlZxI3sXOZZ4/eJ8AcxkQH1+JjSe/ONCEqR9L4Ysq5JdT5ipqtzU7WHalNwzwBv+iE51gNHJNqQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <title>P-Aqro</title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>

    <style>
        .process-type-entry {
            background-color: #38a169;
            /* Green background */
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            /* Rounded corners */
            font-weight: bold;
            text-align: center;
            display: inline-block;
            /* Keeps the span within the td width */
        }

        .process-type-exit {
            background-color: #e53e3e;
            /* Red background */
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            /* Rounded corners */
            font-weight: bold;
            text-align: center;
            display: inline-block;
            /* Keeps the span within the td width */
        }
        .select2-container{
            margin-top: 8px;
        }
        .quantity_error{
            border: 2px solid red !important;
        }
        .note_error{
            border: 2px solid red !important;
        }
    </style>
</head>

<body>
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-200 font-roboto">
        <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="flex-1 flex flex-col overflow-hidden">
            <?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container mx-auto px-6 py-8">
                    <?php echo $__env->yieldContent('body'); ?>
                </div>
            </main>
        </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
    integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.1.1/cdn.js"
    integrity="sha512-KbpTFJv+iXvSHG3l6ixXFeVLbxD0XKJw1zhlqA/nVm9TCufeAPBVbG53uxx6z6pjDdmaa3BXQMkpNeTCgtVySQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // set default time
        var datetimeInput = document.querySelectorAll('[data-datetime-local="true"]');
        if (datetimeInput.length) {
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

            // Set the default value
            for (let i = 0; i < datetimeInput.length; i++) {
                datetimeInput[i].value = formattedDateTime;
            }
        }

        // set time to 00:01 and 23:59 (not current time)
        const startDate = document.getElementById('start_date');
        if(startDate){
            startDate.addEventListener('change', function () {
                const selected = new Date(this.value);
                if (!isNaN(selected.getTime())) {
                    // Set time to 00:01
                    selected.setHours(0);
                    selected.setMinutes(1);
                    selected.setSeconds(0);
                    selected.setMilliseconds(0);
    
                    // Format to 'YYYY-MM-DDTHH:MM'
                    const year = selected.getFullYear();
                    const month = String(selected.getMonth() + 1).padStart(2, '0');
                    const day = String(selected.getDate()).padStart(2, '0');
                    const hours = String(selected.getHours()).padStart(2, '0');
                    const minutes = String(selected.getMinutes()).padStart(2, '0');
    
                    const formatted = `${year}-${month}-${day}T${hours}:${minutes}`;
                    startDate.value = formatted;
                }
            });
        }

        const endDate = document.getElementById('end_date');
        if(endDate){
            endDate.addEventListener('change', function () {
                const selected = new Date(this.value);
                if (!isNaN(selected.getTime())) {
                    // Set time to 23:59
                    selected.setHours(23);
                    selected.setMinutes(59);
                    selected.setSeconds(0);
                    selected.setMilliseconds(0);
    
                    // Format to 'YYYY-MM-DDTHH:MM'
                    const year = selected.getFullYear();
                    const month = String(selected.getMonth() + 1).padStart(2, '0');
                    const day = String(selected.getDate()).padStart(2, '0');
                    const hours = String(selected.getHours()).padStart(2, '0');
                    const minutes = String(selected.getMinutes()).padStart(2, '0');
    
                    const formatted = `${year}-${month}-${day}T${hours}:${minutes}`;
                    endDate.value = formatted;
                }
            });
        }
    });
</script>

<?php echo $__env->yieldContent('script'); ?>

</html>
<?php /**PATH C:\Users\User\Desktop\Alishka Projects\paqro\resources\views/layouts/master.blade.php ENDPATH**/ ?>