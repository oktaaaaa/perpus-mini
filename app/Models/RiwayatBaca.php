<?php

namespace App\Models;

use App\Core\Model;

class RiwayatBaca extends Model
{
    protected string $table = 'riwayat_baca';

    public function milikUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT rb.*, b.judul AS buku_judul, b.penulis AS buku_penulis, b.cover AS buku_cover
             FROM riwayat_baca rb
             JOIN buku b ON b.id = rb.buku_id
             WHERE rb.user_id = :user_id
             ORDER BY rb.terakhir_dibaca DESC"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function catatProgress(int $userId, int $bukuId, int $progress): void
    {
        $progress = max(0, min(100, $progress));
        $stmt = $this->db->prepare(
            "INSERT INTO riwayat_baca (user_id, buku_id, progress, terakhir_dibaca)
             VALUES (:user_id, :buku_id, :progress, NOW())
             ON DUPLICATE KEY UPDATE
                progress = GREATEST(progress, VALUES(progress)),
                terakhir_dibaca = NOW()"
        );
        $stmt->execute(['user_id' => $userId, 'buku_id' => $bukuId, 'progress' => $progress]);
    }
}