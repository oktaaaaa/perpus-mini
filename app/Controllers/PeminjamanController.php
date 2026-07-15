<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    /** Halaman kelola pengajuan peminjaman (khusus admin) */
    public function index(): void
    {
        Auth::requireAdmin();
        $peminjamanModel = new Peminjaman();
        $this->view('admin/peminjaman/index', [
            'menunggu' => $peminjamanModel->allWithRelasi('menunggu'),
        ]);
    }

    /** Member mengajukan peminjaman buku */
    public function ajukan(int $bukuId): void
    {
        Auth::requireMember();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/katalog');
        }

        $peminjamanModel = new Peminjaman();
        $bukuModel = new Buku();
        $userId = Auth::id();

        $buku = $bukuModel->find($bukuId);
        if (!$buku || $buku['stok'] < 1) {
            $this->flash('error', 'Stok buku tidak tersedia.');
            $this->redirect('/katalog');
        }

        if ($peminjamanModel->hitungSedangDipinjam($userId) >= MAX_PINJAM_PER_ANGGOTA) {
            $this->flash('error', 'Anda sudah mencapai batas maksimal ' . MAX_PINJAM_PER_ANGGOTA . ' buku dipinjam bersamaan.');
            $this->redirect('/katalog');
        }

        if ($peminjamanModel->sudahMengajukan($userId, $bukuId)) {
            $this->flash('error', 'Anda sudah mengajukan/meminjam buku ini.');
            $this->redirect('/katalog');
        }

        try {
            $peminjamanModel->insert([
                'user_id' => $userId,
                'buku_id' => $bukuId,
                'status'  => 'menunggu',
            ]);
            $this->flash('success', 'Pengajuan peminjaman berhasil dikirim, menunggu persetujuan admin.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal mengajukan peminjaman.');
        }
        $this->redirect('/katalog');
    }

    /** Admin menyetujui pengajuan */
    public function setujui(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/peminjaman');
        }

        try {
            $peminjamanModel = new Peminjaman();
            $bukuModel = new Buku();
            $pinjam = $peminjamanModel->find($id);

            if (!$pinjam || $pinjam['status'] !== 'menunggu') {
                $this->flash('error', 'Data pengajuan tidak valid.');
                $this->redirect('/admin/peminjaman');
            }

            $lama = (int) $this->input('lama_hari', LAMA_PINJAM_HARI);
            $lama = $lama > 0 ? $lama : LAMA_PINJAM_HARI;

            $peminjamanModel->update($id, [
                'status'              => 'dipinjam',
                'tanggal_pinjam'      => date('Y-m-d'),
                'tanggal_jatuh_tempo' => date('Y-m-d', strtotime("+{$lama} day")),
            ]);
            $bukuModel->kurangiStok($pinjam['buku_id']);

            $this->flash('success', 'Peminjaman disetujui.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal menyetujui peminjaman.');
        }
        $this->redirect('/admin/peminjaman');
    }

    /** Admin menolak pengajuan */
    public function tolak(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/peminjaman');
        }

        try {
            $peminjamanModel = new Peminjaman();
            $peminjamanModel->update($id, ['status' => 'ditolak']);
            $this->flash('success', 'Pengajuan peminjaman ditolak.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal memproses penolakan.');
        }
        $this->redirect('/admin/peminjaman');
    }
}