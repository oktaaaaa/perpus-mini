<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Upload;
use App\Core\Validator;
use App\Models\Buku;
use App\Models\Kategori;

class BukuController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();
        $bukuModel = new Buku();
        $keyword = $this->input('q');

        $this->view('admin/buku/index', [
            'buku' => $bukuModel->allWithKategori($keyword),
            'q'    => $keyword,
        ]);
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $kategoriModel = new Kategori();
        $this->view('admin/buku/form', [
            'kategori' => $kategoriModel->all(),
            'buku'     => null,
        ]);
    }

    public function store(): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/buku');
        }

        $data = $this->validatedInput(false);
        if ($data === null) {
            $this->redirect('/admin/buku/create');
        }

        try {
            $upload = Upload::handle($_FILES['cover'] ?? [], 'covers', ALLOWED_COVER_EXT);
            if (!$upload['ok']) {
                $this->flash('error', $upload['error']);
                $this->setOld($data);
                $this->redirect('/admin/buku/create');
            }
            $data['cover'] = $upload['filename'];

            $bukuModel = new Buku();
            $bukuModel->insert($data);

            $this->flash('success', 'Buku berhasil ditambahkan.');
            $this->redirect('/admin/buku');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Terjadi kesalahan saat menyimpan data buku.');
            $this->redirect('/admin/buku/create');
        }
    }

    public function edit(int $id): void
    {
        Auth::requireAdmin();
        $bukuModel = new Buku();
        $kategoriModel = new Kategori();
        $buku = $bukuModel->find($id);

        if (!$buku) {
            $this->flash('error', 'Buku tidak ditemukan.');
            $this->redirect('/admin/buku');
        }

        $this->view('admin/buku/form', [
            'buku'     => $buku,
            'kategori' => $kategoriModel->all(),
        ]);
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/buku');
        }

        $bukuModel = new Buku();
        $existing = $bukuModel->find($id);
        if (!$existing) {
            $this->flash('error', 'Buku tidak ditemukan.');
            $this->redirect('/admin/buku');
        }

        $data = $this->validatedInput(true);
        if ($data === null) {
            $this->redirect("/admin/buku/{$id}/edit");
        }

        try {
            if (!empty($_FILES['cover']['name'])) {
                $upload = Upload::handle($_FILES['cover'], 'covers', ALLOWED_COVER_EXT);
                if (!$upload['ok']) {
                    $this->flash('error', $upload['error']);
                    $this->redirect("/admin/buku/{$id}/edit");
                }
                $data['cover'] = $upload['filename'];

                // Hapus cover lama agar storage tidak menumpuk
                if (!empty($existing['cover'])) {
                    @unlink(BASE_PATH . '/public/assets/uploads/covers/' . $existing['cover']);
                }
            }

            $bukuModel->update($id, $data);
            $this->flash('success', 'Buku berhasil diperbarui.');
            $this->redirect('/admin/buku');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Terjadi kesalahan saat memperbarui data buku.');
            $this->redirect("/admin/buku/{$id}/edit");
        }
    }

    public function destroy(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/buku');
        }

        try {
            $bukuModel = new Buku();
            $buku = $bukuModel->find($id);
            if ($buku) {
                $bukuModel->delete($id);
                if (!empty($buku['cover'])) {
                    @unlink(BASE_PATH . '/public/assets/uploads/covers/' . $buku['cover']);
                }
                $this->flash('success', 'Buku berhasil dihapus.');
            }
            $this->redirect('/admin/buku');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Buku tidak dapat dihapus karena masih memiliki riwayat transaksi.');
            $this->redirect('/admin/buku');
        }
    }

    private function validatedInput(bool $isEdit = false): ?array
    {
        $judul = trim($this->input('judul', ''));
        $penulis = trim($this->input('penulis', ''));
        $kategoriId = $this->input('kategori_id');
        $deskripsi = trim($this->input('deskripsi', ''));
        $stok = $this->input('stok', '0');
        $ebookPrice = $this->input('ebook_price', '0');
        $rating = $this->input('rating', '0');

        $validator = new Validator([
            'judul' => $judul, 'penulis' => $penulis, 'stok' => $stok, 'ebook_price' => $ebookPrice, 'rating' => $rating,
        ]);
        $validator->required('judul', 'Judul')
            ->required('penulis', 'Penulis')
            ->numeric('stok', 'Stok')
            ->numeric('ebook_price', 'Harga eBook')
            ->numeric('rating', 'Rating');

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->errors();
            $this->setOld(['judul' => $judul, 'penulis' => $penulis, 'deskripsi' => $deskripsi]);
            $this->flash('error', 'Periksa kembali form buku.');
            return null;
        }

        $ratingFloat = max(0, min(5, (float) $rating)); // paksa tetap di rentang 0 - 5

        return [
            'judul'       => $judul,
            'penulis'     => $penulis,
            'kategori_id' => $kategoriId ?: null,
            'deskripsi'   => $deskripsi,
            'stok'        => (int) $stok,
            'ebook_price' => (int) $ebookPrice,
            'rating'      => $ratingFloat,
        ];
    }
}