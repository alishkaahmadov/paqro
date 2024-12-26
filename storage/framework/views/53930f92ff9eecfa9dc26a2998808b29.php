<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ümumi</title>
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

        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        thead {
            background-color: #007bff;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ensures that cells don't expand beyond the page width */
        }

        th,
        td {
            padding: 5px;
            /* Reduce padding to fit more content */
            text-align: left;
            border: 1px solid #ddd;
            vertical-align: top;
            /* Align content to the top of the cell */
            word-wrap: break-word;
            /* Ensure long text wraps within cells */
        }

        tr {
            /* Set a minimum row height */
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Alternate row coloring */
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .page-break {
            page-break-before: always;
        }

        /* Responsive table */
        @media (max-width: 600px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            th,
            td {
                padding: 10px;
            }

            tr {
                margin-bottom: 10px;
            }

            thead {
                display: none;
            }

            tbody tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
                padding: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            td:before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                font-weight: bold;
                text-transform: uppercase;
            }
        }
    </style>
</head>

<body>
    <h1>Ümumi</h1>
    <table>
        <thead>
            <tr>
                <th>Anbar</th>
                <th>Məshul</th>
                <th>Sayı</th>
                <th>Şirkət</th>
                <th>Anbardan</th>
                <th>Anbara</th>
                <th>Subanbar</th>
                <th>Şassi nömrəsi</th>
                <th>DNN nömrəsi</th>
                <th>Tip</th>
                <th>Tarix</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($warehouseName); ?></td>
                    <td><?php echo e($product->product_name); ?> <?php echo e($product->product_code ? '- ' . $product->product_code : ''); ?>

                    </td>
                    <td><?php echo e($product->quantity); ?></td>
                    <td><?php echo e($product->company_name ?? '-'); ?></td>
                    <td><?php echo e($product->from_warehouse ? $product->from_warehouse : '-'); ?></td>
                    <td><?php echo e($product->to_warehouse ? $product->to_warehouse : '-'); ?></td>
                    <td><?php echo e($product->subcategory_name); ?></td>
                    <td><?php echo e($product->highway_code ? $product->highway_code : '-'); ?></td>
                    <td><?php echo e($product->dnn_code ? $product->dnn_code : '-'); ?></td>
                    <td><span
                            class="<?php echo e($warehouseId == $product->to_warehouse_id ? 'process-type-entry' : 'process-type-exit'); ?>"><?php echo e($warehouseId == $product->to_warehouse_id ? 'Giriş' : 'Çıxış'); ?></span>
                    </td>
                    <td><?php echo e($product->entry_date); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>

</html>
<?php /**PATH /var/www/resources/views/pages/pdf/overall.blade.php ENDPATH**/ ?>