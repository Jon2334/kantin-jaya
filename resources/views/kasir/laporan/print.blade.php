<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan - {{ $judulLaporan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #eee; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; font-size: 14px; }
        
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right; margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">🖨️ Cetak Sekarang</button>
    </div>

    <div class="header">
        <h1>KANTIN JAYA</h1>
        <p>Laporan Penjualan</p>
        <p><strong>{{ $judulLaporan }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Order</th>
                <th>Pelanggan</th>
                <th>Metode</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; $no = 1; @endphp
            @foreach($laporan as $order)
                @php 
                    $subtotal = 0;
                    foreach($order->orderDetails as $detail) {
                        $subtotal += $detail->subtotal;
                    }
                    $grandTotal += $subtotal;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>#ORD-{{ $order->id }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td style="text-transform: uppercase;">{{ $order->payment_method }}</td>
                    <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px; text-align: right; margin-right: 30px;">
        <p>Dicetak oleh,</p>
        <br><br><br>
        <p><strong>{{ Auth::user()->name }}</strong></p>
        <p>Kasir Kantin Jaya</p>
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>