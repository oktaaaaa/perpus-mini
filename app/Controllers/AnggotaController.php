<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\User;

class AnggotaController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();
        $userModel = new User();
        $this->view('admin/anggota/index', ['anggota' => $userModel->members()]);
    }

    public function store(): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/anggota');
        }

        $name = trim($this->input('name', ''));
        $email = trim($this->input('email', ''));
        $password = (string) $this->input('password', '');

        $validator = new Validator(['name' => $name, 'email' => $email, 'password' => $password]);
        $validator->required('name', 'Nama')
            ->required('email', 'Email')->email('email')->unique('email', 'Email', 'users')
            ->required('password', 'Password')->min('password', 6, 'Password');

        if ($validator->fails()) {
            $this->flash('error', implode(' ', $validator->errors()));
            $this->redirect('/admin/anggota');
        }

        try {
            $userModel = new User();
            $userModel->insert([
                'name'     => $name,
                'email'    => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role'     => 'member',
            ]);
            $this->flash('success', 'Anggota berhasil ditambahkan.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal menambahkan anggota.');
        }
        $this->redirect('/admin/anggota');
    }

    public function destroy(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/anggota');
        }

        try {
            $userModel = new User();
            $userModel->delete($id);
            $this->flash('success', 'Anggota berhasil dihapus.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Anggota tidak dapat dihapus karena masih memiliki riwayat transaksi.');
        }
        $this->redirect('/admin/anggota');
    }
}