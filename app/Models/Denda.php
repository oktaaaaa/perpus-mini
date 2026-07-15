<?php

namespace App\Models;

use App\Core\Model;
use PDOException;

class Denda extends Model
{
    protected string $table = 'denda';

    public function allWithRelasi(): array
    {
        try {
            $sql = "SELECT denda.*, users.name AS anggota_nama, buku.judul AS buku_judul
                    FROM denda
                    JOIN peminjaman ON peminjaman.id = denda.peminjaman_id
                    JOIN users ON users.id = peminjaman.user_id
                    JOIN buku ON buku.id = peminjaman.buku_id
                    ORDER BY denda.created_at DESC";
            return $this->db->query($sql)->fetchAll();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function totalBelumLunas(): int
    {
        $stmt = $this->db->query("SELECT COALESCE(SUM(jumlah),0) FROM denda WHERE status = 'belum_lunas'");
        return (int) $stmt->fetchColumn();
    }

    public function jumlahDenda(): int
    {
        return $this->count();
    }
}
