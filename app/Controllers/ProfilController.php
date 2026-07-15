<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\User;

class ProfilController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();
        $userModel = new User();
        $user = $userModel->find(Auth::id());

        $this->view('profil/index', ['user' => $user]);
    }

    public function update(): void
    {
        Auth::requireLogin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/profil');
        }

        $userId = Auth::id();
        $userModel = new User();
        $current = $userModel->find($userId);

        $name = trim($this->input('name', ''));
        $email = trim($this->input('email', ''));

        $validator = new Validator(['name' => $name, 'email' => $email]);
        $validator->required('name', 'Nama')
            ->required('email', 'Email')->email('email')
            ->unique('email', 'Email', 'users', $userId);

        if ($validator->fails()) {
            $this->flash('error', implode(' ', $validator->errors()));
            $this->redirect('/profil');
        }

        $data = ['name' => $name, 'email' => $email];

        // Ganti password hanya jika field password baru diisi
        $passwordBaru = (string) $this->input('password_baru', '');
        if ($passwordBaru !== '') {
            $passwordSaatIni = (string) $this->input('password_saat_ini', '');
            $konfirmasi = (string) $this->input('konfirmasi_password', '');

            if (!password_verify($passwordSaatIni, $current['password'])) {
                $this->flash('error', 'Password saat ini yang Anda masukkan salah.');
                $this->redirect('/profil');
            }
            if (strlen($passwordBaru) < 6) {
                $this->flash('error', 'Password baru minimal 6 karakter.');
                $this->redirect('/profil');
            }
            if ($passwordBaru !== $konfirmasi) {
                $this->flash('error', 'Konfirmasi password baru tidak cocok.');
                $this->redirect('/profil');
            }

            $data['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        try {
            $userModel->update($userId, $data);

            // Sinkronkan data session biar nama/email di sidebar langsung update
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;

            $this->flash('success', 'Profil berhasil diperbarui.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal memperbarui profil.');
        }
        $this->redirect('/profil');
    }
}