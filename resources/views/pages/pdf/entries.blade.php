<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Girişlər</title>
    <style>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        thead {
            background-color: #007bff;
            color: #fff;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            font-weight: 600;
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

        /* Responsive table */
        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th, td {
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
    <h1>Girişlər</h1>
    <table>
        <thead>
            <tr>
                <th>Anbar</th>
                <th>Məshul</th>
                <th>Sayı</th>
                <th>Şirkət</th>
                <th>Anbardan</th>
                <th>Kateqoriya</th>
                <th>Giriş tarixi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->to_warehouse }}</td>
                <td>{{ $product->product_name }} {{ $product->product_code ? '- ' . $product->product_code : '' }}</td>
                <td>{{ $product->quantity }}</td>
                <td>{{ $product->company_name ?? "-" }}</td>
                <td>{{ $product->from_warehouse ? $product->from_warehouse : '-' }}</td>
                <td>{{ $product->subcategory_name }}</td>
                <td>{{ $product->entry_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
