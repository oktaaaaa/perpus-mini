<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Wrapper koneksi database berbasis PDO (Prepared Statements).
 * Mendukung MySQL (untuk produksi/pengumpulan tugas) & SQLite (untuk dev/testing lokal).
 */
class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        try {
            if (DB_DRIVER === 'sqlite') {
                $dsn = 'sqlite:' . DB_SQLITE_PATH;
                self::$instance = new PDO($dsn);
                self::$instance->exec('PRAGMA foreign_keys = ON');
            } else {
                $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                self::$instance = new PDO($dsn, DB_USER, DB_PASS);
            }

            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            return self::$instance;
        } catch (PDOException $e) {
            // Jangan tampilkan detail koneksi mentah ke user, catat ke log saja
            error_log('DB Connection Error: ' . $e->getMessage());
            http_response_code(500);
            die('Terjadi kesalahan pada server. Silakan hubungi administrator.');
        }
    }
}
