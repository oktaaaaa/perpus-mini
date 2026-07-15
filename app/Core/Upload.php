<?php

namespace App\Core;

/**
 * Menangani validasi & penyimpanan file upload di sisi server.
 * Validasi ketat: ukuran maksimal & ekstensi (whitelist).
 */
class Upload
{
    /**
     * @return array{ok: bool, filename: ?string, error: ?string}
     */
    public static function handle(array $file, string $subDir, array $allowedExt, int $maxSize = MAX_UPLOAD_SIZE): array
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['ok' => true, 'filename' => null, 'error' => null]; // upload opsional
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'filename' => null, 'error' => 'Terjadi kesalahan saat upload file.'];
        }

        if ($file['size'] > $maxSize) {
            $maxMb = round($maxSize / (1024 * 1024), 1);
            return ['ok' => false, 'filename' => null, 'error' => "Ukuran file maksimal {$maxMb}MB."];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            return ['ok' => false, 'filename' => null, 'error' => 'Ekstensi file tidak diizinkan. Gunakan: ' . implode(', ', $allowedExt)];
        }

        // Validasi MIME asli file (bukan cuma nama), mencegah file .php disamarkan jadi .jpg
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMime = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png', 'webp' => 'image/webp',
            'pdf' => 'application/pdf',
        ];
        if (!in_array($mime, array_intersect_key($allowedMime, array_flip($allowedExt)), true)) {
            return ['ok' => false, 'filename' => null, 'error' => 'Tipe file tidak sesuai dengan ekstensi.'];
        }

        $targetDir = BASE_PATH . '/public/assets/uploads/' . $subDir;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = uniqid($subDir . '_', true) . '.' . $ext;
        $destination = $targetDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['ok' => false, 'filename' => null, 'error' => 'Gagal menyimpan file ke server.'];
        }

        return ['ok' => true, 'filename' => $filename, 'error' => null];
    }
}
