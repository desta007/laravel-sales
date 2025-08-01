<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cetak Barcode {{ $toko->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 1rem;
        }

        .label {
            width: 4in;
            padding: .75rem;
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
            gap: 8px;
            page-break-inside: avoid;
        }

        .barcode {
            text-align: center;
        }

        .meta {
            font-size: 0.85rem;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="label">
        <div class="barcode">
            {!! $barcodeSvg !!}
            <br>
            <strong>{{ $toko->name }}</strong>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
