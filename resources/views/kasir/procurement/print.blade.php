<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran Stok - {{ $tanggal }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
        p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #eee; }
        
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Laporan
        </button>
    </div>

    <div class="header">
        <h1>KANTIN JAYA</h1>
        <p>Laporan Pengeluaran Belanja Stok</p>
        <p>Tanggal Cetak: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal Transaksi</th>
                <th style="width: 25%;">Nama Barang</th>
                <th style="width: 15%;">Jumlah Beli</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 25%;">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($procurements as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $p->inventory->nama_barang }}</td>
                    <td class="text-center">{{ $p->jumlah }} {{ $p->inventory->satuan }}</td>
                    <td class="text-center" style="text-transform: uppercase;">
                        {{ $p->status }}
                    </td>
                    <td class="text-right">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data pengeluaran.</td>
                </tr>
            @endforelse
            
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL PENGELUARAN</td>
                <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right; margin-right: 30px;">
        <p>Mengetahui,</p>
        <p>Manajer Keuangan</p>
        <br><br><br>
        <p><strong>{{ Auth::user()->name }}</strong></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>