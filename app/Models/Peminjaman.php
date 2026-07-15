<?php

namespace App\Models;

use App\Core\Model;
use PDOException;

class Peminjaman extends Model
{
    protected string $table = 'peminjaman';

    public function allWithRelasi(?string $status = null): array
    {
        try {
            $sql = "SELECT peminjaman.*, users.name AS anggota_nama, buku.judul AS buku_judul, buku.id AS buku_id
                    FROM peminjaman
                    JOIN users ON users.id = peminjaman.user_id
                    JOIN buku ON buku.id = peminjaman.buku_id";
            $params = [];
            if ($status) {
                $sql .= " WHERE peminjaman.status = :status";
                $params['status'] = $status;
            }
            $sql .= " ORDER BY peminjaman.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function milikUser(int $userId): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT peminjaman.*, buku.judul AS buku_judul, buku.cover AS buku_cover
                 FROM peminjaman JOIN buku ON buku.id = peminjaman.buku_id
                 WHERE peminjaman.user_id = :uid ORDER BY peminjaman.created_at DESC"
            );
            $stmt->execute(['uid' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function hitungSedangDipinjam(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM peminjaman WHERE user_id = :uid AND status IN ('menunggu','dipinjam')"
        );
        $stmt->execute(['uid' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function sudahMengajukan(int $userId, int $bukuId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM peminjaman WHERE user_id = :uid AND buku_id = :bid AND status IN ('menunggu','dipinjam')"
        );
        $stmt->execute(['uid' => $userId, 'bid' => $bukuId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM peminjaman WHERE status = :s");
        $stmt->execute(['s' => $status]);
        return (int) $stmt->fetchColumn();
    }

    public function countTerlambat(): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam' AND tanggal_jatuh_tempo < :today"
        );
        $stmt->execute(['today' => date('Y-m-d')]);
        return (int) $stmt->fetchColumn();
    }

    public function trenPeminjaman(int $hari = 7): array
    {
        $result = [];
        for ($i = $hari - 1; $i >= 0; $i--) {
            $tanggal = date('Y-m-d', strtotime("-{$i} day"));
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM peminjaman WHERE DATE(created_at) = :tgl"
            );
            $stmt->execute(['tgl' => $tanggal]);
            $result[] = ['tanggal' => date('d M', strtotime($tanggal)), 'jumlah' => (int) $stmt->fetchColumn()];
        }
        return $result;
    }
}
