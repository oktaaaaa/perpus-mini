<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Buku;
use App\Models\PembelianEbook;
use App\Models\ProgresBaca;

class BacaController extends Controller
{
    /** Halaman baca ebook -- hanya bisa diakses kalau sudah beli */
    public function index(int $bukuId): void
    {
        Auth::requireMember();
        $userId = Auth::id();

        $bukuModel = new Buku();
        $pembelianModel = new PembelianEbook();

        $buku = $bukuModel->find($bukuId);
        if (!$buku || !$pembelianModel->sudahBeli($userId, $bukuId)) {
            $this->flash('error', 'Anda belum membeli eBook ini.');
            $this->redirect('/riwayat');
        }

        $progresModel = new ProgresBaca();
        $progresModel->buatJikaBelumAda($userId, $bukuId);

        $this->view('member/baca', ['buku' => $buku]);
    }

    /** Dipanggil via fetch()/AJAX dari halaman baca utk simpan progress */
    public function updateProgress(int $bukuId): void
    {
        Auth::requireMember();
        if (!$this->verifyCsrf()) {
            http_response_code(419);
            echo json_encode(['ok' => false, 'message' => 'Sesi tidak valid.']);
            return;
        }

        $progress = (int) $this->input('progress', 0);
        $progresModel = new ProgresBaca();
        $progresModel->buatJikaBelumAda(Auth::id(), $bukuId);
        $progresModel->tambahProgress(Auth::id(), $bukuId, $progress);

        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
    }
}