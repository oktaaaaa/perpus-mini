<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PerpusModern | Sistem Informasi Perpustakaan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-[#0b0b16] text-slate-200 min-h-screen">

<header class="flex items-center justify-between px-6 md:px-14 py-5 border-b border-white/5">
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-pink-500"></div>
        <span class="font-bold text-white">PerpusModern</span>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= url('/login') ?>" class="text-sm text-slate-400 hover:text-white">Masuk</a>
        <a href="<?= url('/register') ?>" class="text-sm px-4 py-2 rounded-lg btn-primary text-white">Daftar Gratis</a>
    </div>
</header>

<section class="text-center px-6 py-16 max-w-3xl mx-auto">
    <h1 class="text-3xl md:text-4xl font-bold text-white leading-tight">
        Jelajahi Dunia <br>
        <span class="bg-gradient-to-r from-violet-400 to-pink-400 bg-clip-text text-transparent">Koleksi Buku Terbaik</span>
    </h1>
    <p class="mt-4 text-sm text-slate-400">Sistem informasi perpustakaan modern — pinjam buku, beli eBook, dan kelola semua dari satu tempat.</p>

    <form action="<?= url('/register') ?>" class="mt-6 flex max-w-lg mx-auto">
        <input type="text" readonly placeholder="Ketik judul buku, penulis..."
            class="flex-1 px-4 py-2.5 rounded-l-lg bg-white/5 border border-white/10 text-sm outline-none">
        <button class="px-5 py-2.5 rounded-r-lg btn-primary text-white text-sm">Cari</button>
    </form>

    <div class="mt-5 flex justify-center gap-3">
        <a href="<?= url('/register') ?>" class="px-5 py-2.5 rounded-lg btn-primary text-white text-sm font-medium">Daftar Gratis — Jadi Anggota</a>
        <a href="<?= url('/login') ?>" class="px-5 py-2.5 rounded-lg border border-white/10 text-sm">Sudah punya akun? Masuk</a>
    </div>
</section>

<section class="px-6 md:px-14 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mb-16">
    <div class="card p-4 text-center">
        <p class="text-xl font-bold text-white"><?= (int) $totalBuku ?></p>
        <p class="text-xs text-slate-500">Koleksi Buku</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-xl font-bold text-white"><?= (int) $totalAnggota ?></p>
        <p class="text-xs text-slate-500">Anggota Aktif</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-xl font-bold text-white"><?= (int) $totalKategori ?></p>
        <p class="text-xs text-slate-500">Kategori</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-xl font-bold text-white"><?= (int) $sedangDipinjam ?></p>
        <p class="text-xs text-slate-500">Sedang Dipinjam</p>
    </div>
</section>

<section class="px-6 md:px-14 pb-6 flex flex-wrap gap-2 max-w-5xl mx-auto">
    <span class="px-4 py-1.5 rounded-full bg-white/10 text-xs text-white">Semua</span>
    <?php foreach (array_slice($kategori, 0, 8) as $k): ?>
        <span class="px-4 py-1.5 rounded-full border border-white/10 text-xs text-slate-400"><?= e($k['nama']) ?></span>
    <?php endforeach; ?>
</section>

<section class="px-6 md:px-14 pb-20 max-w-5xl mx-auto">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        <?php foreach (array_slice($buku, 0, 8) as $b): ?>
            <div class="card overflow-hidden">
                <div class="h-40 bg-gradient-to-br from-violet-900/40 to-pink-900/20 flex items-center justify-center">
                    <?php if (!empty($b['cover'])): ?>
                        <img src="<?= asset('uploads/covers/' . e($b['cover'])) ?>" class="h-full w-full object-cover" alt="<?= e($b['judul']) ?>">
                    <?php else: ?>
                        <span class="text-3xl">📖</span>
                    <?php endif; ?>
                </div>
                <div class="p-3">
                    <p class="text-sm font-medium text-white truncate"><?= e($b['judul']) ?></p>
                    <p class="text-xs text-slate-500 truncate"><?= e($b['penulis']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="text-center text-sm text-slate-500 mt-8">
        <a href="<?= url('/register') ?>" class="text-violet-400 hover:underline">Daftar sekarang</a> untuk melihat katalog lengkap & mulai meminjam.
    </p>
</section>

<footer class="text-center text-xs text-slate-600 py-6 border-t border-white/5">
    &copy; <?= date('Y') ?> PerpusModern &mdash; Tugas Pengembangan Aplikasi Web Dinamis
</footer>
</body>
</html>