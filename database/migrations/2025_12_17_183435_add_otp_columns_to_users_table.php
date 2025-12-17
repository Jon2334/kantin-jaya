<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan kode OTP (misal: 123456)
            // Kita buat nullable karena user yang sudah verified tidak butuh OTP lagi
            $table->string('otp_code')->nullable()->after('password');

            // Menambahkan kolom waktu kadaluarsa OTP
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom jika dilakukan rollback
            $table->dropColumn(['otp_code', 'otp_expires_at']);
        });
    }
};