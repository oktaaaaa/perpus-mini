<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();
        $kategoriModel = new Kategori();
        $this->view('admin/kategori/index', ['kategori' => $kategoriModel->all()]);
    }

    public function store(): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/kategori');
        }

        $nama = trim($this->input('nama', ''));
        $validator = new Validator(['nama' => $nama]);
        $validator->required('nama', 'Nama kategori')->unique('nama', 'Nama kategori', 'kategori');

        if ($validator->fails()) {
            $this->flash('error', implode(' ', $validator->errors()));
            $this->redirect('/admin/kategori');
        }

        try {
            $kategoriModel = new Kategori();
            $kategoriModel->insert([
                'nama' => $nama,
                'slug' => Kategori::slugify($nama),
            ]);
            $this->flash('success', 'Kategori berhasil ditambahkan.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal menambahkan kategori.');
        }
        $this->redirect('/admin/kategori');
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/kategori');
        }

        $nama = trim($this->input('nama', ''));
        $validator = new Validator(['nama' => $nama]);
        $validator->required('nama', 'Nama kategori')->unique('nama', 'Nama kategori', 'kategori', $id);

        if ($validator->fails()) {
            $this->flash('error', implode(' ', $validator->errors()));
            $this->redirect('/admin/kategori');
        }

        try {
            $kategoriModel = new Kategori();
            $kategoriModel->update($id, [
                'nama' => $nama,
                'slug' => Kategori::slugify($nama),
            ]);
            $this->flash('success', 'Kategori berhasil diperbarui.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal memperbarui kategori.');
        }
        $this->redirect('/admin/kategori');
    }

    public function destroy(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/kategori');
        }

        try {
            $kategoriModel = new Kategori();
            $kategoriModel->delete($id);
            $this->flash('success', 'Kategori berhasil dihapus.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Kategori tidak dapat dihapus karena masih dipakai oleh buku.');
        }
        $this->redirect('/admin/kategori');
    }
}