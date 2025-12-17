<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Variable ini kita buat PUBLIC.
     * Di Laravel, property public pada Mailable otomatis 
     * akan dikirim dan bisa dibaca di file View (Blade).
     */
    public $otp;

    /**
     * Create a new message instance.
     * Menerima kode OTP dari Controller saat class ini dipanggil.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the message envelope.
     * Mengatur Subjek Email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi (OTP) - Kantin Jaya',
        );
    }

    /**
     * Get the message content definition.
     * Mengatur file View (Blade) mana yang akan dirender.
     */
    public function content(): Content
    {
        return new Content(
            // Pastikan Anda sudah membuat file: resources/views/emails/otp.blade.php
            view: 'emails.otp', 
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}