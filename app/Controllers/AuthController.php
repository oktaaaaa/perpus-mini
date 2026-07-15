<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? '/dashboard' : '/katalog');
        }
        $this->view('auth/login', [], null);
    }

    public function login(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid, silakan coba lagi.');
            $this->redirect('/login');
        }

        $email = trim($this->input('email', ''));
        $password = (string) $this->input('password', '');

        $validator = new Validator(['email' => $email, 'password' => $password]);
        $validator->required('email', 'Email')->email('email')->required('password', 'Password');

        if ($validator->fails()) {
            $this->setOld(['email' => $email]);
            $this->flash('error', 'Periksa kembali data yang Anda masukkan.');
            $this->redirect('/login');
        }

        if (!Auth::attempt($email, $password)) {
            $this->setOld(['email' => $email]);
            $this->flash('error', 'Email atau password salah.');
            $this->redirect('/login');
        }

        $this->redirect(Auth::isAdmin() ? '/dashboard' : '/katalog');
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? '/dashboard' : '/katalog');
        }
        $this->view('auth/register', [], null);
    }

    public function register(): void
    {
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid, silakan coba lagi.');
            $this->redirect('/register');
        }

        $name = trim($this->input('name', ''));
        $email = trim($this->input('email', ''));
        $password = (string) $this->input('password', '');

        $validator = new Validator(['name' => $name, 'email' => $email, 'password' => $password]);
        $validator->required('name', 'Nama')
            ->required('email', 'Email')->email('email')
            ->unique('email', 'Email', 'users')
            ->required('password', 'Password')->min('password', 6, 'Password');

        if ($validator->fails()) {
            $this->setOld(['name' => $name, 'email' => $email]);
            $_SESSION['errors'] = $validator->errors();
            $this->redirect('/register');
        }

        try {
            $userModel = new User();
            $userModel->insert([
                'name'     => $name,
                'email'    => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role'     => 'member', // registrasi publik selalu jadi member biasa
            ]);

            $this->flash('success', 'Registrasi berhasil! Silakan login.');
            $this->redirect('/login');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Registrasi gagal, silakan coba lagi.');
            $this->redirect('/register');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}