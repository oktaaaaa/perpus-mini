<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Upload;
use App\Models\Buku;
use App\Models\PembelianEbook;
use App\Models\ProgresBaca;

class EbookController extends Controller
{
    /** Manajemen eBook untuk Admin: riwayat pembelian & pendapatan */
    public function bukti(int $id): void
{
    Auth::requireAdmin();

    $pembelianModel = new PembelianEbook();

    $data = $pembelianModel->find($id);

    if (!$data) {
        die('Data tidak ditemukan');
    }

    $this->view('admin/ebook/bukti', [
        'data' => $data
    ]);
}
    public function adminIndex(): void
    {
        Auth::requireAdmin();
        $pembelianModel = new PembelianEbook();
        $this->view('admin/ebook/index', [
            'pembelian'       => $pembelianModel->allWithRelasi(),
            'totalPembelian'  => count($pembelianModel->all()),
            'totalPendapatan' => $pembelianModel->totalPendapatan(),
        ]);
    }

    /** Toko eBook untuk Member */
    public function store(): void
    {
        Auth::requireMember();
        $bukuModel = new Buku();
        $pembelianModel = new PembelianEbook();

        $bukuList = array_filter($bukuModel->allWithKategori(), fn($b) => (int) $b['ebook_price'] > 0);
        $userId = Auth::id();

        $this->view('member/ebook_store', [
            'buku'      => $bukuList,
            'sudahBeli' => array_map(fn($b) => (int) $b['buku_id'], $pembelianModel->milikUser($userId)),
        ]);
    }

    /** Proses pembelian eBook dengan upload bukti pembayaran */
    public function beli(int $bukuId): void
    {
        Auth::requireMember();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/ebook');
        }

        $bukuModel = new Buku();
        $pembelianModel = new PembelianEbook();
        $userId = Auth::id();

        $buku = $bukuModel->find($bukuId);
        if (!$buku || (int) $buku['ebook_price'] <= 0) {
            $this->flash('error', 'eBook tidak tersedia.');
            $this->redirect('/ebook');
        }

        if ($pembelianModel->sudahBeli($userId, $bukuId)) {
            $this->flash('error', 'Anda sudah membeli eBook ini.');
            $this->redirect('/ebook');
        }

        try {
            $upload = Upload::handle($_FILES['bukti_bayar'] ?? [], 'bukti', ALLOWED_BUKTI_EXT);
            if (!$upload['ok']) {
                $this->flash('error', $upload['error'] ?? 'Bukti pembayaran wajib diunggah.');
                $this->redirect('/ebook');
            }
            if (!$upload['filename']) {
                $this->flash('error', 'Bukti pembayaran wajib diunggah.');
                $this->redirect('/ebook');
            }

            $pembelianModel->insert([
                'user_id'     => $userId,
                'buku_id'     => $bukuId,
                'harga'       => $buku['ebook_price'],
                'bukti_bayar' => $upload['filename'],
            ]);

            // Otomatis buat baris "Riwayat Baca" untuk eBook yang baru dibeli
            (new ProgresBaca())->buatJikaBelumAda($userId, $bukuId);

            $this->flash('success', 'Pembelian eBook berhasil! Silakan cek halaman Riwayat.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal memproses pembelian eBook.');
        }
        $this->redirect('/ebook');
    }

    /** Member klik "Lanjut Baca" -> tambah progress baca eBook yang sudah dibeli */
    public function baca(int $bukuId): void
    {
        Auth::requireMember();
        $pembelianModel = new PembelianEbook();
        $userId = Auth::id();

        if (!$pembelianModel->sudahBeli($userId, $bukuId)) {
            $this->flash('error', 'Anda belum membeli eBook ini.');
            $this->redirect('/riwayat');
        }

        $progresModel = new ProgresBaca();
        $progresModel->buatJikaBelumAda($userId, $bukuId); // jaga-jaga kalau belum ada baris progres
        $progresModel->tambahProgress($userId, $bukuId, 20);

        $this->flash('success', 'Progress baca diperbarui.');
        $this->redirect('/riwayat');
    }

    /** Member memberi rating & ulasan untuk eBook yang sudah dibeli */
    public function rating(int $bukuId): void
    {
        Auth::requireMember();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/riwayat');
        }

        $rating = (int) $this->input('rating', 0);
        $ulasan = trim($this->input('ulasan', ''));

        if ($rating < 1 || $rating > 5) {
            $this->flash('error', 'Rating harus antara 1 - 5.');
            $this->redirect('/riwayat');
        }

        $progresModel = new ProgresBaca();
        $progresModel->beriRating(Auth::id(), $bukuId, $rating, $ulasan);

        $this->flash('success', 'Terima kasih atas ulasannya!');
        $this->redirect('/riwayat');
    }

    /** Member download file eBook yang sudah dibeli */
    public function download(int $bukuId): void
    {
        Auth::requireMember();
        $bukuModel = new Buku();
        $pembelianModel = new PembelianEbook();
        $userId = Auth::id();

        if (!$pembelianModel->sudahBeli($userId, $bukuId)) {
            $this->flash('error', 'Anda belum membeli eBook ini.');
            $this->redirect('/riwayat');
        }

        $buku = $bukuModel->find($bukuId);
        $path = BASE_PATH . '/public/assets/uploads/ebooks/' . ($buku['ebook_file'] ?? '');

        if (empty($buku['ebook_file']) || !file_exists($path)) {
            $this->flash('error', 'File eBook belum tersedia. Hubungi admin.');
            $this->redirect('/riwayat');
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}