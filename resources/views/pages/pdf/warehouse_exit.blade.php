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
            font-size: 14px;
            font-weight: bold;
        }

        thead {
        }

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
        .title{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .py-1{
            padding: 4px 0;
        }
        .mb-4{
            margin-bottom: 16px;
        }
        .mt-4{
            margin-top: 16px;
        }
        .under-text{
            display: block;
            font-size: .8em;
            margin-top: 2px;
        }
        .footer{
            position: relative;
            padding: 40px 0;
        }
        .sign-image {
            width: 200px;
            height: 200px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .sign-image img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="">
        <div class="py-1" style="text-align: center">"P-AQRO" MMC (VÖEN 1404226851)</div>
        <div class="py-1" style="text-align: center">(ciddi hesabat blanklarının sifarişçisi hüquqi şəxs olduqda onun adı və VÖEN-i, fiziki şəxs olduqda adı, soyadı, a.a və VÖEN-i)</div>
        <div class="py-1" style="text-align: center">MALLARIN (MATERİALLARIN) TƏSƏRRÜFATDAXİLİ YERDƏYİŞMƏSİ QAİMƏ-FAKTURASI</div>
        <div class="py-1 mb-4" style="text-align: center">05.10.2024</div>
    </div>
    <div class="mb-4">Kimdən: P-Aqro MMC-nin Anbar Müdiri Səfərov Asəf Cəmşid oğlundan</div>
    <div class="mb-4">Kimə: {{$to}}na</div>
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
            @for ($i = 0; $i < count($products); $i++)
                <tr>
                    <td>{{+$i + 1}}</td>
                    <td>{{ $codes[$i] }}</td>
                    <td>{{ $products[$i] }}</td>
                    <td>{{ $notes[$i] }}</td>
                    <td>{{ $quantities[$i] }}</td>
                    <td>{{ $categories[$i] }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
    <div class="footer">
        <div class="mb-4 mt-4">
            Təhvil verdim: P-Aqro MMC-nin Anbar Müdiri SƏFƏROV ASƏF CƏMŞİD OĞLU
            <span class="under-text">(vəzifəsi, soyadı, adı, atasının adı)</span>
        </div>
        <div class="mt-4">  
            Təhvil aldım: {{$to}}
            <span class="under-text">(vəzifəsi, soyadı, adı, atasının adı)</span>
        </div>
        <div class="sign-image">
            <img src="{{ $base64Image }}" alt="Signature" style="width: 100%; height: auto;">
        </div>
    </div>
</body>
</html>
