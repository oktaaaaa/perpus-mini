<?php
/**
 * Fungsi-fungsi bantuan global (helper) yang dipakai di seluruh view/controller.
 */

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return APP_URL . $path;
}

function asset(string $path = ''): string
{
    return url('/assets/' . ltrim($path, '/'));
}

/** Escape output untuk mencegah XSS */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function rupiah($angka): string
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

function tanggal_indo(?string $date): string
{
    if (!$date) {
        return '-';
    }
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function old_input(string $key, string $default = ''): string
{
    $value = $_SESSION['old'][$key] ?? $default;
    return e($value);
}

function flash_get(string $key): ?string
{
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}
