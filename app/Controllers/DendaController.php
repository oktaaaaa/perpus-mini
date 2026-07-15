<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Denda;

class DendaController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();
        $dendaModel = new Denda();
        $this->view('admin/denda/index', [
            'denda'           => $dendaModel->allWithRelasi(),
            'totalBelumLunas' => $dendaModel->totalBelumLunas(),
            'jumlahDenda'     => $dendaModel->jumlahDenda(),
        ]);
    }

    public function lunasi(int $id): void
    {
        Auth::requireAdmin();
        if (!$this->verifyCsrf()) {
            $this->flash('error', 'Sesi tidak valid.');
            $this->redirect('/admin/denda');
        }

        try {
            $dendaModel = new Denda();
            $dendaModel->update($id, ['status' => 'lunas']);
            $this->flash('success', 'Denda ditandai lunas.');
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            $this->flash('error', 'Gagal memperbarui status denda.');
        }
        $this->redirect('/admin/denda');
    }
}