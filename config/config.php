<?php
/**
 * Konfigurasi utama aplikasi.
 * Membaca file .env jika ada (format sederhana KEY=VALUE).
 */

function env_load(string $path): void
{
    if (!file_exists($path)) {
        return;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value);
        if ($key !== '' && getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

env_load(dirname(__DIR__) . '/.env');

function env(string $key, $default = null)
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

// Zona waktu
date_default_timezone_set('Asia/Jakarta');

// Base path project (untuk generate URL & routing agar tetap jalan di sub-folder)
define('BASE_PATH', dirname(__DIR__));
define('APP_URL', rtrim(env('APP_URL', ''), '/'));

// Konfigurasi Database
define('DB_DRIVER', env('DB_DRIVER', 'mysql'));      // mysql | sqlite
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'perpus_mini'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_SQLITE_PATH', env('DB_SQLITE_PATH', BASE_PATH . '/database/perpus_mini.sqlite'));

// Aturan bisnis
define('MAX_PINJAM_PER_ANGGOTA', 3);   // maksimal buku dipinjam bersamaan
define('LAMA_PINJAM_HARI', 7);         // default lama peminjaman
define('DENDA_PER_HARI', 1000);        // Rp per hari keterlambatan

// Upload
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_COVER_EXT', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_BUKTI_EXT', ['jpg', 'jpeg', 'png', 'pdf']);

session_name('perpus_session');
session_start();
