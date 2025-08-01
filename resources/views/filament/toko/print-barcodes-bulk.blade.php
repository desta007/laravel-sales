<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cetak Barcode Banyak</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .label {
            width: 4in;
            padding: .5rem;
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
            gap: 6px;
            page-break-inside: avoid;
        }

        .barcode {
            text-align: center;
        }

        .meta {
            font-size: 0.75rem;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    @foreach ($barcodes as $entry)
        @php
            $toko = $entry['toko'];
            $svg = $entry['svg'];
        @endphp
        <div class="label">
            <div class="barcode">
                {!! $svg !!}
                <br>
                <strong>{{ $toko->name }}</strong>
            </div>
        </div>
    @endforeach

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
