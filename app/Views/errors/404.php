<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>404 - Halaman Tidak Ditemukan</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-[#0b0b16] text-slate-200 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <p class="text-7xl font-bold bg-gradient-to-r from-violet-400 to-pink-400 bg-clip-text text-transparent">404</p>
        <p class="mt-2 text-slate-400">Halaman yang Anda cari tidak ditemukan.</p>
        <a href="<?= url('/') ?>" class="inline-block mt-6 px-5 py-2 rounded-lg btn-primary text-white text-sm">Kembali ke Beranda</a>
    </div>
</body>
</html>