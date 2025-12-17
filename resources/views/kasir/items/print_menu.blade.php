<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Menu - {{ $tanggal }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            color: #000;
            padding: 20px;
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
            vertical-align: middle; /* Agar teks berada di tengah vertikal foto */
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Style Khusus Foto */
        .menu-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #ddd;
            display: block;
            margin: 0 auto; /* Tengah horizontal */
            background-color: #eee;
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
            /* Paksa browser mencetak background warna (untuk header tabel) */
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

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
        <p>Laporan Daftar Menu & Stok</p>
        <p>Tanggal Cetak: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Foto</th> 
                <th style="width: 35%;">Nama Menu</th>
                <th style="width: 20%;">Harga Satuan</th>
                <th style="width: 10%;">Stok</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{-- LOGIKA BARU: Cek Base64 dulu, baru URL biasa --}}
                        @if(!empty($item->base64_image))
                            {{-- Prioritas 1: Gunakan Base64 dari Controller (Aman untuk Print) --}}
                            <img src="{{ $item->base64_image }}" alt="{{ $item->nama }}" class="menu-img">
                        @elseif(!empty($item->image))
                            {{-- Prioritas 2: Gunakan URL Asli (Jika Controller gagal convert) --}}
                            {{-- HAPUS fungsi asset() karena Cloudinary sudah full URL --}}
                            <img src="{{ $item->image }}" alt="{{ $item->nama }}" class="menu-img">
                        @else
                            {{-- Prioritas 3: Placeholder jika tidak ada gambar --}}
                            <span style="font-size: 10px; color: #999;">No IMG</span>
                        @endif
                    </td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->stok }}</td>
                    <td class="text-center">
                        @if($item->stok == 0)
                            <span style="color: red; font-weight: bold;">HABIS</span>
                        @elseif($item->stok <= 5)
                            <span style="color: orange; font-weight: bold;">KRITIS</span>
                        @else
                            <span style="color: green; font-weight: bold;">AMAN</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data menu.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <p>Mengetahui,</p>
        <p>Manajer Kantin</p>
        <div class="line"></div>
        <p>{{ Auth::user()->name ?? 'Administrator' }}</p>
    </div>

    <script>
        // Script otomatis print saat halaman terbuka
        window.onload = function() {
            // Kita beri waktu 1 detik agar gambar Base64 ter-render sempurna sebelum dialog print muncul
            setTimeout(function() {
                window.print();
            }, 1000);
        }
    </script>

</body>
</html>