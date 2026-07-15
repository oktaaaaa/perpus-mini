<?php

namespace App\Models;

use App\Core\Model;
use PDOException;

class PembelianEbook extends Model
{
    protected string $table = 'pembelian_ebook';

    public function allWithRelasi(): array
    {
        try {
            $sql = "SELECT pembelian_ebook.*, users.name AS anggota_nama, buku.judul AS buku_judul
                    FROM pembelian_ebook
                    JOIN users ON users.id = pembelian_ebook.user_id
                    JOIN buku ON buku.id = pembelian_ebook.buku_id
                    ORDER BY pembelian_ebook.created_at DESC";
            return $this->db->query($sql)->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function milikUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT pembelian_ebook.*, buku.judul AS buku_judul, buku.ebook_file AS file
             FROM pembelian_ebook JOIN buku ON buku.id = pembelian_ebook.buku_id
             WHERE pembelian_ebook.user_id = :uid ORDER BY pembelian_ebook.created_at DESC"
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function sudahBeli(int $userId, int $bukuId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM pembelian_ebook WHERE user_id = :uid AND buku_id = :bid"
        );
        $stmt->execute(['uid' => $userId, 'bid' => $bukuId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function totalPendapatan(): int
    {
        $stmt = $this->db->query("SELECT COALESCE(SUM(harga),0) FROM pembelian_ebook");
        return (int) $stmt->fetchColumn();
    }
}
