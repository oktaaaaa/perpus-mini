<?php

namespace App\Models;

use App\Core\Model;
use PDOException;

class ProgresBaca extends Model
{
    protected string $table = 'progres_baca';

    /** Daftar riwayat baca milik 1 user, join ke buku untuk judul & penulis */
    public function milikUser(int $userId): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT progres_baca.*, buku.judul AS buku_judul, buku.penulis AS buku_penulis
                 FROM progres_baca
                 JOIN buku ON buku.id = progres_baca.buku_id
                 WHERE progres_baca.user_id = :uid
                 ORDER BY progres_baca.terakhir_dibaca DESC"
            );
            $stmt->execute(['uid' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /** Buat baris progres baru saat eBook baru dibeli (progress mulai dari 0) */
    public function buatJikaBelumAda(int $userId, int $bukuId): void
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT id FROM progres_baca WHERE user_id = :uid AND buku_id = :bid LIMIT 1"
            );
            $stmt->execute(['uid' => $userId, 'bid' => $bukuId]);
            if ($stmt->fetch()) {
                return; // sudah ada
            }

            $this->insert([
                'user_id'         => $userId,
                'buku_id'         => $bukuId,
                'progress'        => 0,
                'terakhir_dibaca' => date('Y-m-d H:i:s'),
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    /** Tambah progress baca (dipanggil tiap kali user klik "Lanjut Baca") */
    public function tambahProgress(int $userId, int $bukuId, int $tambahan = 20): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE progres_baca
                 SET progress = LEAST(100, progress + :tambahan), terakhir_dibaca = :sekarang
                 WHERE user_id = :uid AND buku_id = :bid"
            );
            return $stmt->execute([
                'tambahan' => $tambahan,
                'sekarang' => date('Y-m-d H:i:s'),
                'uid'      => $userId,
                'bid'      => $bukuId,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /** Simpan rating & ulasan dari member untuk 1 eBook */
    public function beriRating(int $userId, int $bukuId, int $rating, string $ulasan): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE progres_baca SET rating = :rating, ulasan = :ulasan
                 WHERE user_id = :uid AND buku_id = :bid"
            );
            return $stmt->execute([
                'rating' => max(1, min(5, $rating)),
                'ulasan' => $ulasan,
                'uid'    => $userId,
                'bid'    => $bukuId,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}