<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anbar çıxış</title>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* Body styles */
        body {
            padding: 10px 40px;
            color: #333;
            font-size: 10px;
            font-weight: bold;
            font-family: DejaVu Sans !important;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: separate;
            /* Avoid issues with border-collapse */
            border-spacing: 0;
            /* Remove gaps between table borders */
            font-size: 8px;
            border: 1px solid black;
            margin-bottom: 40px;
            /* Ensure outer border is present */
        }

        /* Table cells */
        th,
        td {
            padding: 2px 10px;
            text-align: center;
            border: 1px solid black !important;
            /* Force cell borders to render */
            vertical-align: top;
            word-wrap: break-word;
        }

        /* Prevent rows from breaking between pages */
        tr {
            page-break-inside: avoid;
        }

        /* Ensure table header stays on top */
        thead {
            display: table-header-group;
        }


        /* Ensure tbody fills available space */
        tbody::after {
            content: '';
            display: table-row;
            height: 100%;
            /* Pushes rows to take up extra space */
        }

        /* Hover effect for table rows */
        tr:hover {
            background-color: #f1f1f1;
        }

        /* Last row border adjustment */
        tbody tr:last-child td {
            border-bottom: 1px solid black !important;
        }

        /* Zebra striping for table rows */
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Title alignment styles */
        .title {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Utility classes for padding and margins */
        .py-1 {
            padding: 4px 0;
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .mt-4 {
            margin-top: 16px;
        }

        /* Underline and subtext styles */
        .underline {
            border-bottom: 1px solid black;
        }

        .under-text {
            display: block;
            font-size: 0.8em;
            margin-top: 2px;
        }

        /* Footer styles */
        .footer {
            position: relative;
            padding: 40px 0;
        }

        .relative {
            position: relative;
        }

        /* Signature image container */
        .sign-image {
            width: 150px;
            height: 150px;
            position: absolute;
            left: 2px;
            top: -65px;
            text-align: center;
        }

        .sign-image img {
            max-width: 100%;
            height: auto;
        }

        /* Table layout for signatures and approvals */
        .table2 {
            display: table;
            width: 100%;
        }

        .table-cell2 {
            display: table-cell;
            vertical-align: top;
        }

        /* Width utility classes */
        .w-10 {
            width: 10%;
        }

        .w-20 {
            width: 20%;
        }

        .w-30 {
            width: 30%;
        }

        .w-70 {
            width: 70%;
        }

        .min-h-1 {
            min-height: 1em;
        }

        /* Block display for elements */
        .block {
            display: block;
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
        <div class="py-1 mb-4" style="text-align: center">{{ $transfer_date }}</div>
    </div>
    <div class="mb-4 underline">
        Kimdən: P-Aqro MMC-nin Anbar Müdiri SƏFƏROV ASƏF CƏMŞİD oğlundan
        <span class="under-text">(vəzifəsi, soyadı, adı, atasının adı)</span>
    </div>
    <div class="mb-4" style="text-align: center">{{ $pdfDocNumber }}</div>
    <div class="mb-4 underline">Kimə: {{ $warehouseName }} filialının Anbardarı {{ $to }}na</div>
    <table>
        <thead style="display: table-header-group;">
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
            @for ($i = 0; $i < count($products); $i++)
                @if (!!($products[$i] && $quantities[$i]))
                    <tr>
                        <td>{{ +$i + 1 }}</td>
                        <td>{{ $codes[$i] }}</td>
                        <td>{{ $products[$i] }}</td>
                        <td>{{ $notes[$i] }}</td>
                        <td>{{ $quantities[$i] }}</td>
                        <td>{{ $categories[$i] }}</td>
                    </tr>
                @endif
            @endfor
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
                            <img src="{{ $base64Image }}" alt="Signature" style="width: 100%; height: auto;">
                        </div>
                    </div>
                    <span class="under-text">(imza və tarix)</span>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <div class="table2">
                <div class="table-cell2 w-70">
                    <div class="underline">Təhvil aldım: {{ $warehouseName }} filialının Anbardarı {{ $to }}
                    </div>
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
