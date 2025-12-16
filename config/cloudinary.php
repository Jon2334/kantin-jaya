<?php

/*
 * File ini harus ada agar Vercel bisa membaca konfigurasi Cloudinary.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Cloudinary.
    |
    */
    'cloud_url' => env('CLOUDINARY_URL'),

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'ml_default'),

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration (Alternative)
    |--------------------------------------------------------------------------
    |
    | Jika CLOUDINARY_URL gagal, kita bisa pakai manual setting di bawah ini.
    | Pastikan Anda menambahkan Env Var ini di Vercel jika cara URL gagal.
    |
    */
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key'    => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
];