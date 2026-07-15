<?php

namespace App\Models;

use App\Core\Model;

class Kategori extends Model
{
    protected string $table = 'kategori';

    public function all(string $orderBy = 'nama ASC'): array
    {
        return parent::all($orderBy);
    }

    public function countBukuByKategori(int $kategoriId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM buku WHERE kategori_id = :id");
        $stmt->execute(['id' => $kategoriId]);
        return (int) $stmt->fetchColumn();
    }

    public static function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
