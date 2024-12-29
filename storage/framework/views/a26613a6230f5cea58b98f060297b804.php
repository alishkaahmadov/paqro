<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anbar çıxış</title>
    <style>
        * {
            font-family: DejaVu Sans !important;
        }

        body {
            padding: 10px 40px;
            color: #333;
            padding-bottom: 100px;
            font-size: 10px;
            font-weight: bold;
        }

        thead {}

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            width: auto;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            vertical-align: top;
            word-wrap: break-word;
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

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .title {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .py-1 {
            padding: 4px 0;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .under-text {
            display: block;
            font-size: .8em;
            margin-top: 2px;
        }

        .footer {
            position: relative;
            padding: 40px 0;
        }
        .relative{
            position: relative;
        }

        .sign-image {
            width: 75px;
            height: 75px;
            position: absolute;
            left: 20px;
            top: -45px;
            /* top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); */
            text-align: center;
        }
        .sign-image img {
            max-width: 100%;
            height: auto;
        }
        .underline {
            border-bottom: 1px solid black;
        }

        .block {
            display: block;
        }
        .w-10{
            width: 10%;
        }
        .w-20{
            width: 20%;
        }
        .w-30{
            width: 30%;
        }
        .w-70{
            width: 70%;
        }
        .min-h-1{
            min-height: 1em;
        }
        .table2 {
            display: table;
            width: 100%;
        }
        .table-cell2 {
            display: table-cell;
            vertical-align: top;
        }
    </style>
</head>

<body>
    <div class="">
        <div class="py-1" style="text-align: center">"P-AQRO" MMC (VÖEN 1404226851)</div>
        <hr>
        <div class="py-1" style="text-align: center">(ciddi hesabat blanklarının sifarişçisi hüquqi şəxs olduqda onun
            adı və VÖEN-i, fiziki şəxs olduqda adı, soyadı, a.a və VÖEN-i)</div>
        <div class="py-1" style="text-align: center">MALLARIN (MATERİALLARIN) TƏSƏRRÜFATDAXİLİ YERDƏYİŞMƏSİ
            QAİMƏ-FAKTURASI</div>
        <div class="py-1" style="text-align: center">Seriya AA nömrə</div>
        <div class="py-1 mb-4" style="text-align: center"><?php echo e($transfer_date); ?></div>
    </div>
    <div class="mb-4 underline">
        Kimdən: P-Aqro MMC-nin Anbar Müdiri SƏFƏROV ASƏF CƏMŞİD oğlundan
        <span class="under-text">(vəzifəsi, soyadı, adı, atasının adı)</span>
    </div>
    <div class="mb-4" style="text-align: center"><?php echo e($pdfDocNumber); ?></div>
    <div class="mb-4 underline">Kimə: <?php echo e($warehouseName); ?> filialının Anbardarı <?php echo e($to); ?>na</div>
    <table>
        <thead>
            <tr>
                <th>№</th>
                <th>KOD</th>
                <th>Qiymətlilərin adı</th>
                <th>Ölçü vahidi</th>
                <th>Miqdarı</th>
                <th>Kateqoriya</th>
            </tr>
        </thead>
        <tbody>
            <?php for($i = 0; $i < count($products); $i++): ?>
                <?php if(!!($codes[$i] && $products[$i] && $quantities[$i])): ?>
                    <tr>
                        <td><?php echo e(+$i + 1); ?></td>
                        <td><?php echo e($codes[$i]); ?></td>
                        <td><?php echo e($products[$i]); ?></td>
                        <td><?php echo e($notes[$i]); ?></td>
                        <td><?php echo e($quantities[$i]); ?></td>
                        <td><?php echo e($categories[$i]); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endfor; ?>
        </tbody>
    </table>
    <div class="footer">
        <div class="mb-4 mt-4">
            <div class="table2">
                <div class="table-cell2 w-70">
                    <div class="underline">Təhvil verdim: P-Aqro MMC-nin Anbar Müdiri SƏFƏROV ASƏF CƏMŞİD OĞLU</div>
                    <span class="under-text">(vəzifəsi, soyadı, adı, atasının adı)</span>
                </div>
                <div class="w-10"></div>
                <div class="table-cell2 w-20">
                    <div class="underline min-h-1 relative">
                        <div class="sign-image">
                            <img src="<?php echo e($base64Image); ?>" alt="Signature" style="width: 100%; height: auto;">
                        </div>
                    </div>
                    <span class="under-text">(imza və tarix)</span>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="table2">
                <div class="table-cell2 w-70">
                    <div class="underline">Təhvil aldım: <?php echo e($warehouseName); ?> filialının Anbardarı <?php echo e($to); ?></div>
                    <span class="under-text">(vəzifəsi, soyadı, adı, atasının adı)</span>
                </div>
                <div class="w-10"></div>
                <div class="table-cell2 w-20">
                    <div class="underline min-h-1"></div>
                    <span class="under-text">(imza və tarix)</span>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <span class="underline block">Rəhbər:</span>
            <span class="under-text">(İmza və tarix)</span>
        </div>
        <div class="mt-4">
            <span class="underline block">M.Y.</span>
            <span class="under-text">(Ciddi hesabat blanklarını çap edən şəxsin adı, VÖEN-i)</span>
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\Users\User\Desktop\Alishka Projects\paqro\resources\views/pages/pdf/warehouse_exit.blade.php ENDPATH**/ ?>