<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use App\Models\Peminjaman;

class HomeController extends Controller
{
    public function index(): void
    {
        $bukuModel = new Buku();
        $kategoriModel = new Kategori();
        $userModel = new User();
        $peminjamanModel = new Peminjaman();

        $this->view('home/index', [
            'buku'          => $bukuModel->allWithKategori(),
            'kategori'      => $kategoriModel->all(),
            'totalBuku'     => $bukuModel->count(),
            'totalAnggota'  => $userModel->countMembers(),
            'totalKategori' => $kategoriModel->count(),
            'sedangDipinjam' => $peminjamanModel->countByStatus('dipinjam'),
        ], null);
    }
}