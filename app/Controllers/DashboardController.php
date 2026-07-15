<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Buku;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\Denda;

class DashboardController extends Controller
{
    /** Dashboard khusus Admin (proteksi backend-side) */
    public function admin(): void
    {
        Auth::requireAdmin();

        $bukuModel = new Buku();
        $userModel = new User();
        $peminjamanModel = new Peminjaman();
        $dendaModel = new Denda();

        $this->view('admin/dashboard', [
            'totalBuku'      => $bukuModel->count(),
            'totalAnggota'   => $userModel->countMembers(),
            'sedangDipinjam' => $peminjamanModel->countByStatus('dipinjam'),
            'menunggu'       => $peminjamanModel->countByStatus('menunggu'),
            'terlambat'      => $peminjamanModel->countTerlambat(),
            'tren'           => $peminjamanModel->trenPeminjaman(7),
            'bukuPopuler'    => $bukuModel->bukuPopuler(4),
            'totalDendaBelumLunas' => $dendaModel->totalBelumLunas(),
        ]);
    }
}