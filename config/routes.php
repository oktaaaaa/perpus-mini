<?php

use App\Controllers\AnggotaController;
use App\Controllers\AuthController;
use App\Controllers\BukuController;
use App\Controllers\DashboardController;
use App\Controllers\DendaController;
use App\Controllers\EbookController;
use App\Controllers\HomeController;
use App\Controllers\KatalogController;
use App\Controllers\KategoriController;
use App\Controllers\PengembalianController;
use App\Controllers\PeminjamanController;
use App\Controllers\ProfilController;
use App\Core\Router;

/** @var Router $router */
$router->get('/admin/ebook/bukti/{id}', [
    \App\Controllers\EbookController::class,
    'bukti'
]);
// ------------------------------------------------------------
// Publik
// ------------------------------------------------------------
$router->get('/', [HomeController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// ------------------------------------------------------------
// Dashboard (redirect otomatis sesuai role di controller)
// ------------------------------------------------------------
$router->get('/dashboard', [DashboardController::class, 'admin']);

// ------------------------------------------------------------
// Profil (bisa diakses Admin & Member)
// ------------------------------------------------------------
$router->get('/profil', [ProfilController::class, 'index']);
$router->post('/profil', [ProfilController::class, 'update']);

// ------------------------------------------------------------
// Area Member
// ------------------------------------------------------------
$router->get('/katalog', [KatalogController::class, 'index']);
$router->get('/katalog/{id}', [KatalogController::class, 'detail']);
$router->get('/riwayat', [KatalogController::class, 'riwayat']);
$router->post('/riwayat/{id}/ajukan-kembali', [KatalogController::class, 'ajukanKembali']);
$router->post('/pinjam/{bukuId}', [PeminjamanController::class, 'ajukan']);
$router->get('/ebook', [EbookController::class, 'store']);
$router->post('/ebook/{bukuId}/beli', [EbookController::class, 'beli']);
$router->get('/ebook/{bukuId}/download', [EbookController::class, 'download']);
$router->post('/ebook/{bukuId}/rating', [EbookController::class, 'rating']);
$router->get('/baca/{bukuId}', [EbookController::class, 'baca']);

// ------------------------------------------------------------
// Area Admin (proteksi backend di masing-masing controller)
// ------------------------------------------------------------
$router->get('/admin/buku', [BukuController::class, 'index']);
$router->get('/admin/buku/create', [BukuController::class, 'create']);
$router->post('/admin/buku', [BukuController::class, 'store']);
$router->get('/admin/buku/{id}/edit', [BukuController::class, 'edit']);
$router->post('/admin/buku/{id}', [BukuController::class, 'update']);
$router->post('/admin/buku/{id}/hapus', [BukuController::class, 'destroy']);

$router->get('/admin/kategori', [KategoriController::class, 'index']);
$router->post('/admin/kategori', [KategoriController::class, 'store']);
$router->post('/admin/kategori/{id}', [KategoriController::class, 'update']);
$router->post('/admin/kategori/{id}/hapus', [KategoriController::class, 'destroy']);

$router->get('/admin/anggota', [AnggotaController::class, 'index']);
$router->post('/admin/anggota', [AnggotaController::class, 'store']);
$router->post('/admin/anggota/{id}/hapus', [AnggotaController::class, 'destroy']);

$router->get('/admin/peminjaman', [PeminjamanController::class, 'index']);
$router->post('/admin/peminjaman/{id}/setujui', [PeminjamanController::class, 'setujui']);
$router->post('/admin/peminjaman/{id}/tolak', [PeminjamanController::class, 'tolak']);

$router->get('/admin/pengembalian', [PengembalianController::class, 'index']);
$router->post('/admin/pengembalian/{id}/terima', [PengembalianController::class, 'terima']);
$router->post('/admin/pengembalian/{id}/denda', [PengembalianController::class, 'catatDenda']);

$router->get('/admin/denda', [DendaController::class, 'index']);
$router->post('/admin/denda/{id}/lunasi', [DendaController::class, 'lunasi']);

$router->get('/admin/ebook', [EbookController::class, 'adminIndex']);