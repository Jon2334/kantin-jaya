<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #ORD-{{ $order->id }} - Kantin Jaya</title>
    <style>
        /* Reset CSS dasar untuk struk */
        body {
            font-family: 'Courier New', Courier, monospace; /* Font monospace agar angka lurus */
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0; /* Background abu-abu di layar monitor */
        }

        /* Kotak Struk (Preview di Layar) */
        .receipt {
            width: 80mm; /* Lebar standar kertas struk thermal */
            margin: 20px auto;
            padding: 15px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: 1px dashed #aaa;
        }

        /* Helper Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        /* Garis Putus-putus Pemisah */
        .border-dashed {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }

        /* Tabel Item */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th, .items-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        /* Margin Helper */
        .my-2 { margin-top: 8px; margin-bottom: 8px; }
        
        /* Tombol Aksi (Disembunyikan saat print) */
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            padding: 8px 16px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 0 5px;
        }
        .btn-print { background-color: #4f46e5; color: white; }
        .btn-close { background-color: #ef4444; color: white; }

        /* --- MEDIA QUERY UNTUK MODE CETAK (PRINT) --- */
        @media print {
            body { 
                background-color: #fff; 
                margin: 0; 
            }
            .receipt {
                width: 100%; /* Full width saat print */
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }
            .actions {
                display: none !important; /* Sembunyikan tombol saat print */
            }
        }
    </style>
</head>
<body>

    <div class="receipt">
        <!-- Header Toko -->
        <div class="text-center">
            <h2 style="margin: 0; font-size: 16px;">KANTIN JAYA</h2>
            <p style="margin: 2px 0; font-size: 10px;">Jl. Pendidikan Teknologi Informasi No. 1</p>
            <p style="margin: 2px 0; font-size: 10px;">Telp: 0812-3456-7890</p>
        </div>

        <div class="border-dashed"></div>

        <!-- Informasi Transaksi -->
        <div>
            <table style="width: 100%">
                <tr>
                    <td>No. Order</td>
                    <td class="text-right font-bold">#ORD-{{ $order->id }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td class="text-right">{{ Auth::user()->name }}</td>
                </tr>
                <tr>
                    <td>Pelanggan</td>
                    <td class="text-right">{{ $order->user->name }}</td>
                </tr>
            </table>
        </div>

        <div class="border-dashed"></div>

        <!-- Daftar Item Belanja -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-left" style="width: 40%;">Menu</th>
                    <th class="text-center" style="width: 20%;">Qty</th>
                    <th class="text-right" style="width: 40%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($order->orderDetails as $detail)
                    <tr>
                        <td>
                            {{ $detail->item->nama }}
                            <div style="font-size: 10px; color: #555;">@ {{ number_format($detail->harga_satuan, 0, ',', '.') }}</div>
                        </td>
                        <td class="text-center">{{ $detail->qty }}</td>
                        <td class="text-right">
                            {{ number_format($detail->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @php $grandTotal += $detail->subtotal; @endphp
                @endforeach
            </tbody>
        </table>

        <div class="border-dashed"></div>

        <!-- Total Pembayaran -->
        <table style="width: 100%">
            <tr class="font-bold" style="font-size: 14px;">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td class="text-right uppercase">{{ $order->payment_method }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td class="text-right uppercase">LUNAS</td>
            </tr>
        </table>

        <div class="border-dashed"></div>

        <!-- Footer -->
        <div class="text-center my-2">
            <p style="margin: 5px 0;">Terima Kasih Atas Kunjungan Anda</p>
            <p style="margin: 0; font-size: 10px;"><i>Wifi Password: kantinjaya123</i></p>
        </div>
        <div class="text-center">
            <p style="font-size: 10px;">- Simpan struk sebagai bukti pembayaran -</p>
        </div>

        <!-- Tombol Aksi (Hanya tampil di layar monitor) -->
        <div class="actions">
            <button onclick="window.print()" class="btn btn-print">🖨️ Cetak</button>
            <button onclick="window.close()" class="btn btn-close">❌ Tutup</button>
        </div>
    </div>

    <script>
        // Otomatis munculkan dialog print saat halaman selesai dimuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500); // Delay sedikit agar CSS ter-load sempurna
        };
    </script>
</body>
</html>