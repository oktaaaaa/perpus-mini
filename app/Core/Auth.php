<?php

namespace App\Core;

use App\Models\User;

/**
 * Menangani autentikasi & otorisasi (RBAC).
 * Password di-hash dengan password_hash()/password_verify().
 */
class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $userModel = new User();
        $user = $userModel->findBy('email', $email);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];

        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    /** Wajib login, kalau tidak lempar ke halaman login */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            $_SESSION['flash']['error'] = 'Silakan login terlebih dahulu.';
            header('Location: ' . url('/login'));
            exit;
        }
    }

    /**
     * Proteksi URL backend-side: hanya admin yang boleh lewat.
     * Jika bukan admin (misal member menembak URL admin), tolak & lempar ke dashboard-nya sendiri.
     */
    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            $_SESSION['flash']['error'] = 'Akses ditolak. Halaman tersebut khusus Admin.';
            header('Location: ' . url('/katalog'));
            exit;
        }
    }

    public static function requireMember(): void
    {
        self::requireLogin();
        if (self::isAdmin()) {
            // admin tidak perlu halaman member, arahkan ke dashboard admin
            header('Location: ' . url('/dashboard'));
            exit;
        }
    }
}
