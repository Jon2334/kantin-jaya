<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi OTP</title>
    <style>
        /* Reset CSS sederhana agar tampilan konsisten di berbagai email client */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545E;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background-color: #4f46e5; /* Warna Biru Utama */
            padding: 20px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .email-body {
            padding: 30px;
        }
        .otp-box {
            background-color: #f3f4f6;
            border: 2px dashed #4f46e5;
            color: #4f46e5;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            letter-spacing: 8px; /* Memberi jarak antar angka */
            border-radius: 8px;
        }
        .warning-text {
            font-size: 12px;
            color: #ef4444; /* Warna Merah */
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            padding: 20px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="email-wrapper">
            <div class="email-header">
                <h1>KANTIN JAYA</h1>
            </div>

            <div class="email-body">
                <p style="font-size: 16px;">Halo,</p>
                <p>Terima kasih telah mendaftar di <strong>Kantin Jaya</strong>. Untuk menyelesaikan proses pendaftaran dan mengaktifkan akun Anda, silakan gunakan kode verifikasi (OTP) berikut:</p>
                
                <div class="otp-box">
                    {{ $otp }}
                </div>

                <p style="text-align: center;">Atau salin kode di atas dan masukkan ke halaman verifikasi.</p>

                <div style="text-align: center;">
                   <p class="warning-text">⚠️ Kode ini hanya berlaku selama 5 menit.</p>
                </div>

                <p style="margin-top: 30px; font-size: 14px; color: #666;">
                    Jika Anda tidak merasa melakukan pendaftaran ini, abaikan saja email ini. Akun Anda tidak akan aktif tanpa kode di atas.
                </p>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} Kantin Jaya System. All rights reserved.</p>
                <p>Ini adalah pesan otomatis, mohon tidak membalas email ini.</p>
            </div>
        </div>
    </div>

</body>
</html>