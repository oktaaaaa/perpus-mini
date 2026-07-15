<?php
use App\Core\Auth;
$user = Auth::user();
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$isActive = fn(string $path) => str_starts_with($currentPath, ltrim($path, '/')) ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perpus | <?= e($title ?? 'Dashboard') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-[#0b0b16] text-slate-200 min-h-screen flex">

<aside class="w-64 shrink-0 bg-[#100f1d] border-r border-white/5 min-h-screen flex flex-col">
    <div class="px-5 py-5 flex items-center gap-2 border-b border-white/5">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center font-bold">P</div>
        <div>
            <p class="font-bold text-white leading-none">Perpus</p>
            <p class="text-[10px] text-slate-500 tracking-wide">DIGITAL LIBRARY</p>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <?php if (Auth::isAdmin()): ?>
            <p class="px-3 pt-2 pb-1 text-[10px] uppercase text-slate-600 tracking-widest">Menu Utama</p>
            <a href="<?= url('/dashboard') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('dashboard') ?>">Dashboard</a>
            <a href="<?= url('/admin/buku') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('admin/buku') ?>">Koleksi Buku</a>
            <a href="<?= url('/admin/kategori') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('admin/kategori') ?>">Kategori</a>
            <a href="<?= url('/admin/anggota') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('admin/anggota') ?>">Anggota</a>
            <p class="px-3 pt-4 pb-1 text-[10px] uppercase text-slate-600 tracking-widest">Transaksi</p>
            <a href="<?= url('/admin/peminjaman') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('admin/peminjaman') ?>">Peminjaman</a>
            <a href="<?= url('/admin/pengembalian') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('admin/pengembalian') ?>">Pengembalian</a>
            <a href="<?= url('/admin/denda') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('admin/denda') ?>">Denda</a>
            <a href="<?= url('/admin/ebook') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('admin/ebook') ?>">eBook</a>
        <?php else: ?>
            <p class="px-3 pt-2 pb-1 text-[10px] uppercase text-slate-600 tracking-widest">Menu Anggota</p>
            <a href="<?= url('/katalog') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('katalog') ?>">Katalog Buku</a>
            <a href="<?= url('/riwayat') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('riwayat') ?>">Riwayat Peminjaman</a>
            <a href="<?= url('/ebook') ?>" class="flex items-center gap-2 px-3 py-2 rounded-lg <?= $isActive('ebook') ?>">eBook Store</a>
        <?php endif; ?>
    </nav>

    <div class="p-4 border-t border-white/5">
        <a href="<?= url('/profil') ?>" class="flex items-center justify-center gap-2 text-sm <?= $isActive('profil') ?> border border-white/10 rounded-lg py-2">Profil Saya</a>
        <a href="<?= url('/logout') ?>" class="flex items-center justify-center gap-2 text-sm text-slate-400 hover:text-white border border-white/10 rounded-lg py-2">Keluar</a>
    </div>
</aside>

<main class="flex-1 min-h-screen">
    <header class="flex items-center justify-between px-8 py-4 border-b border-white/5">
        <div>
            <h1 class="text-lg font-bold text-white"><?= e($title ?? 'Dashboard') ?></h1>
            <p class="text-xs text-slate-500"><?= date('l, d F Y') ?></p>
        </div>
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-sm font-bold text-white">
                <?= e(mb_substr($user['name'] ?? '?', 0, 1)) ?>
            </div>
            <div class="text-sm leading-tight">
                <p class="text-white font-medium"><?= e($user['name'] ?? '') ?></p>
                <p class="text-slate-500 text-xs capitalize"><?= e($user['role'] ?? '') ?></p>
            </div>
        </div>
    </header>

    <div class="p-8">
        <?php if ($msg = flash_get('success')): ?>
            <div class="mb-5 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash_get('error')): ?>
            <div class="mb-5 px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm"><?= e($msg) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</main>
</body>
</html>
