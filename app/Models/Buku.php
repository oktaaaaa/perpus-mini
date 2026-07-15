<?php

namespace App\Models;

use App\Core\Model;
use PDOException;

class Buku extends Model
{
    protected string $table = 'buku';

    /** Ambil semua buku beserta nama kategori (relasi) */
    public function allWithKategori(?string $keyword = null, ?int $kategoriId = null): array
    {
        try {
            $sql = "SELECT buku.*, kategori.nama AS kategori_nama
                    FROM buku
                    LEFT JOIN kategori ON kategori.id = buku.kategori_id
                    WHERE 1=1";
            $params = [];

            if ($keyword) {
                $sql .= " AND (buku.judul LIKE :kw OR buku.penulis LIKE :kw)";
                $params['kw'] = "%{$keyword}%";
            }
            if ($kategoriId) {
                $sql .= " AND buku.kategori_id = :kat";
                $params['kat'] = $kategoriId;
            }
            $sql .= " ORDER BY buku.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function findWithKategori(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT buku.*, kategori.nama AS kategori_nama
                 FROM buku LEFT JOIN kategori ON kategori.id = buku.kategori_id
                 WHERE buku.id = :id LIMIT 1"
            );
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function bukuPopuler(int $limit = 4): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT buku.*, COUNT(peminjaman.id) AS total_pinjam
                 FROM buku
                 LEFT JOIN peminjaman ON peminjaman.buku_id = buku.id
                 GROUP BY buku.id
                 ORDER BY total_pinjam DESC, buku.created_at DESC
                 LIMIT :lim"
            );
            $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function kurangiStok(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE buku SET stok = stok - 1 WHERE id = :id AND stok > 0");
        return $stmt->execute(['id' => $id]);
    }

    public function tambahStok(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE buku SET stok = stok + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
