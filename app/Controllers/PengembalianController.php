<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Buku;
use App\Models\Denda;
use App\Models\Peminjaman;

class PengembalianController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();
        $peminjamanModel = new Peminjaman();
        $this->view('admin/pengembalian/index', [
            'dipinjam' => $peminjamanModel->allWithRelasi('dipinjam'),
        ]);
    }

    /** Proses "Terima Buku" -- otomatis hitung denda jika terlambat */
    public function terima(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/pengembalian');
        }

        try {
            $peminjamanModel = new Peminjaman();
            $bukuModel = new Buku();
            $dendaModel = new Denda();

            $pinjam = $peminjamanModel->find($id);
            if (!$pinjam || $pinjam['status'] !== 'dipinjam') {
                $this->flash('error', 'Data peminjaman tidak valid.');
                $this->redirect('/admin/pengembalian');
            }

            $hariIni = date('Y-m-d');
            $peminjamanModel->update($id, [
                'status'          => 'dikembalikan',
                'tanggal_kembali' => $hariIni,
            ]);
            $bukuModel->tambahStok($pinjam['buku_id']);

            // Hitung keterlambatan otomatis
            if (!empty($pinjam['tanggal_jatuh_tempo']) && $hariIni > $pinjam['tanggal_jatuh_tempo']) {
                $telat = (strtotime($hariIni) - strtotime($pinjam['tanggal_jatuh_tempo'])) / 86400;
                $telat = (int) ceil($telat);
                $jumlahDenda = $telat * DENDA_PER_HARI;

                $dendaModel->insert([
                    'peminjaman_id' => $id,
                    'jenis'         => 'terlambat',
                    'jumlah'        => $jumlahDenda,
                    'keterangan'    => "Terlambat {$telat} hari (otomatis)",
                    'status'        => 'belum_lunas',
                ]);
            }

            $this->flash('success', 'Pengembalian buku berhasil diproses.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal memproses pengembalian.');
        }
        $this->redirect('/admin/pengembalian');
    }

    /** Catat denda manual: buku rusak/hilang */
    public function catatDenda(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/pengembalian');
        }

        $jenis = $this->input('jenis', 'rusak');
        $jumlah = (int) $this->input('jumlah', 0);
        $keterangan = trim($this->input('keterangan', ''));

        if (!in_array($jenis, ['rusak', 'hilang'], true) || $jumlah <= 0) {
            $this->flash('error', 'Data denda tidak valid.');
            $this->redirect('/admin/pengembalian');
        }

        try {
            $dendaModel = new Denda();
            $dendaModel->insert([
                'peminjaman_id' => $id,
                'jenis'         => $jenis,
                'jumlah'        => $jumlah,
                'keterangan'    => $keterangan ?: ucfirst($jenis),
                'status'        => 'belum_lunas',
            ]);
            $this->flash('success', 'Denda berhasil dicatat.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal mencatat denda.');
        }
        $this->redirect('/admin/pengembalian');
    }
}