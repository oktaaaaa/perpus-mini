<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;

class KatalogController extends Controller
{
    /** Katalog buku untuk Member: cari, filter kategori, ajukan pinjam */
    public function index(): void
    {
        Auth::requireMember();
        $bukuModel = new Buku();
        $kategoriModel = new Kategori();
        $peminjamanModel = new Peminjaman();

        $keyword = $this->input('q');
        $kategoriId = $this->input('kategori_id') ?: null;

        $this->view('member/katalog', [
            'buku'           => $bukuModel->allWithKategori($keyword, $kategoriId ? (int) $kategoriId : null),
            'kategori'       => $kategoriModel->all(),
            'q'              => $keyword,
            'kategoriId'     => $kategoriId,
            'sedangDipinjam' => $peminjamanModel->hitungSedangDipinjam(Auth::id()),
        ]);
    }

    /** Riwayat Saya milik member yang sedang login (peminjaman fisik + pembelian eBook) */
    /** Riwayat Saya milik member yang sedang login (pembelian, baca, peminjaman fisik) */
    public function riwayat(): void
    {
        Auth::requireMember();
        $peminjamanModel = new Peminjaman();
        $pembelianModel = new \App\Models\PembelianEbook();
        $progresModel = new \App\Models\ProgresBaca();

        $this->view('member/riwayat', [
            'transaksi'      => $pembelianModel->milikUser(Auth::id()),
            'riwayat_baca'   => $progresModel->milikUser(Auth::id()),
            'riwayat_pinjam' => $peminjamanModel->milikUser(Auth::id()),
        ]);
    }

    /** Halaman detail 1 buku (sinopsis lengkap) sebelum member meminjam */
    public function detail(int $id): void
    {
        Auth::requireMember();
        $bukuModel = new Buku();
        $peminjamanModel = new Peminjaman();

        $buku = $bukuModel->findWithKategori($id);
        if (!$buku) {
            $this->flash('error', 'Buku tidak ditemukan.');
            $this->redirect('/katalog');
        }

        $this->view('member/detail_buku', [
            'buku'           => $buku,
            'sedangDipinjam' => $peminjamanModel->hitungSedangDipinjam(Auth::id()),
            'sudahMengajukan' => $peminjamanModel->sudahMengajukan(Auth::id(), $id),
        ]);
    }

    /** Halaman browsing Kategori untuk Member (klik kategori -> filter katalog) */
    public function kategoriList(): void
    {
        Auth::requireMember();
        $kategoriModel = new Kategori();
        $kategoriList = $kategoriModel->all();

        $data = array_map(function ($k) use ($kategoriModel) {
            $k['jumlah_buku'] = $kategoriModel->countBukuByKategori((int) $k['id']);
            return $k;
        }, $kategoriList);

        $this->view('member/kategori', ['kategori' => $data]);
    }

    /** Member menandai buku sudah dikembalikan, menunggu verifikasi admin */
    public function ajukanKembali(int $id): void
    {
        Auth::requireMember();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/riwayat');
        }

        try {
            $peminjamanModel = new Peminjaman();
            $ok = $peminjamanModel->ajukanKembali($id, Auth::id());
            if ($ok) {
                $this->flash('success', 'Pengembalian berhasil diajukan. Silakan serahkan buku ke admin/petugas untuk diverifikasi.');
            } else {
                $this->flash('error', 'Data peminjaman tidak valid atau sudah diproses.');
            }
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal mengajukan pengembalian.');
        }
        $this->redirect('/riwayat');
    }
}