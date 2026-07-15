<?php

namespace App\Core;

/**
 * Base Controller: menyediakan helper umum untuk semua controller turunan.
 */
abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/app'): void
    {
        extract($data);
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View tidak ditemukan: {$view}");
        }

        if ($layout) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            require BASE_PATH . '/app/Views/' . $layout . '.php';
        } else {
            require $viewFile;
        }
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function flash(string $key, ?string $message = null)
    {
        if ($message !== null) {
            $_SESSION['flash'][$key] = $message;
            return null;
        }
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    protected function old(string $key, $default = '')
    {
        $value = $_SESSION['old'][$key] ?? $default;
        unset($_SESSION['old'][$key]);
        return $value;
    }

    protected function setOld(array $data): void
    {
        $_SESSION['old'] = $data;
    }

    /** Verifikasi token CSRF sederhana */
    protected function verifyCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
