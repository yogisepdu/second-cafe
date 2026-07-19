<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>
        QR Code {{ $cafeTable->table_number }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px;
            background: #f8fafc;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
        }

        .qr-card {
            overflow: hidden;
            padding: 36px;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.10);
            text-align: center;
        }

        .brand {
            margin: 0;
            color: #d97706;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        h1 {
            margin: 12px 0 6px;
            font-size: 32px;
        }

        .location {
            margin: 0;
            color: #6b7280;
        }

        .qr-wrapper {
            margin: 28px auto;
            padding: 16px;
            border: 2px dashed #f59e0b;
            border-radius: 20px;
            background: #fffbeb;
        }

        .qr-wrapper img {
            display: block;
            width: 100%;
            max-width: 340px;
            height: auto;
            margin: auto;
        }

        .instruction {
            margin: 0;
            color: #374151;
            font-size: 17px;
            line-height: 1.6;
        }

        .url {
            overflow-wrap: anywhere;
            margin-top: 20px;
            padding: 12px;
            border-radius: 10px;
            background: #f3f4f6;
            color: #6b7280;
            font-size: 12px;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 24px;
        }

        .button {
            display: inline-block;
            padding: 12px 18px;
            border: 0;
            border-radius: 10px;
            background: #f59e0b;
            color: #111827;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }

        .button-secondary {
            background: #111827;
            color: #ffffff;
        }

        @media print {
            body {
                padding: 0;
                background: #ffffff;
            }

            .qr-card {
                border: 0;
                box-shadow: none;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <main class="container">
        <section class="qr-card">
            <p class="brand">Second Cafe</p>

            <h1>
                {{ $cafeTable->table_number }}
            </h1>

            <p class="location">
                {{ $cafeTable->name ?: 'Pemesanan Menu' }}
            </p>

            <div class="qr-wrapper">
                <img alt="QR Code {{ $cafeTable->table_number }}" height="340" src="{{ $qrCodeDataUri }}"
                    width="340">
            </div>

            <p class="instruction">
                Pindai QR Code untuk melihat menu dan
                melakukan pemesanan.
            </p>

            <div class="url">
                {{ $orderingUrl }}
            </div>

            <div class="actions">
                <a class="button" href="{{ route('admin.cafe-tables.qr.download', $cafeTable) }}">
                    Unduh QR Code
                </a>

                <button class="button button-secondary" onclick="window.print()" type="button">
                    Cetak
                </button>
            </div>
        </section>
    </main>
</body>

</html>
