<!DOCTYPE html>
<html>
<head>
    <title>INVOICE #PO-{{ $procurement->id }}</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .total { font-size: 20px; font-weight: bold; text-align: right; margin-top: 20px; }
        .stamp { border: 2px solid red; color: red; transform: rotate(-15deg); display: inline-block; padding: 5px 20px; font-weight: bold; font-size: 24px; position: absolute; right: 50px; top: 200px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE PEMBELIAN</h1>
        <p>PT. SUPPLIER MAKMUR JAYA</p>
    </div>

    <div class="info">
        <p><strong>Kepada:</strong> {{ $procurement->user->name }} (Kantin Jaya)</p>
        <p><strong>Tanggal:</strong> {{ $procurement->created_at->format('d M Y') }}</p>
        <p><strong>No. Invoice:</strong> #PO-{{ $procurement->id }}</p>
    </div>

    @if($procurement->status == 'paid' || $procurement->status == 'shipped')
        <div class="stamp">LUNAS</div>
    @else
        <div class="stamp" style="border-color:orange; color:orange;">BELUM BAYAR</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Qty</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $procurement->inventory->nama_barang }}</td>
                <td>{{ $procurement->jumlah }} {{ $procurement->inventory->satuan }}</td>
                <td>Rp {{ number_format($procurement->total_harga, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        TOTAL TAGIHAN: Rp {{ number_format($procurement->total_harga, 0, ',', '.') }}
    </div>

    <div style="margin-top: 50px; text-align: center;">
        <p>Hormat Kami,</p>
        <br><br>
        <p>( Supplier Admin )</p>
    </div>

    <script>window.print();</script>
</body>
</html>