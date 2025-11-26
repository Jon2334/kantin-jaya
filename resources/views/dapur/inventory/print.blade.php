<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Dapur - {{ $tanggal }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 10pt;
        }
        .signature {
            margin-top: 60px;
            margin-right: 20px;
            text-align: center;
            float: right;
            width: 200px;
        }
        .signature p {
            margin: 0;
        }
        .line {
            border-top: 1px solid #000;
            margin-top: 50px;
        }

        /* Sembunyikan tombol cetak saat diprint */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Print (Hanya tampil di layar) -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Laporan
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #ef4444; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h1>KANTIN JAYA</h1>
        <p>Laporan Stok Bahan Baku Dapur</p>
        <p>Tanggal Cetak: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 40%;">Nama Bahan</th>
                <th style="width: 20%;">Sisa Stok</th>
                <th style="width: 15%;">Satuan</th>
                <th style="width: 20%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-center">{{ (float)$item->stok }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td class="text-center">
                        @if($item->stok <= 5)
                            KRITIS
                        @else
                            AMAN
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Data stok kosong.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <p>Mengetahui,</p>
        <p>Kepala Dapur</p>
        <div class="line"></div>
        <p>{{ Auth::user()->name }}</p>
    </div>

    <script>
        // Otomatis muncul dialog print saat halaman dibuka
        window.onload = function() {
            window.print();
        }
    </script>

</body>
</html>