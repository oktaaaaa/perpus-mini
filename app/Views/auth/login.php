<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk | Perpus Digital</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-[#0b0b16] text-slate-200 min-h-screen flex items-center justify-center px-4">
<div class="w-full max-w-4xl grid md:grid-cols-2 card overflow-hidden">
    <div class="p-10 hidden md:flex flex-col justify-center bg-gradient-to-br from-violet-600/20 to-pink-600/10">
        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-violet-500 to-pink-500 mb-6"></div>
        <h2 class="text-2xl font-bold text-white">Selamat Datang <span class="bg-gradient-to-r from-violet-400 to-pink-400 bg-clip-text text-transparent">Kembali!</span></h2>
        <p class="mt-2 text-sm text-slate-400">Masuk ke akun Anda dan lanjutkan perjalanan membaca bersama perpustakaan digital kami.</p>
    </div>

    <div class="p-10">
        <h3 class="text-lg font-bold text-white mb-1">Masuk ke Akun</h3>
        <p class="text-sm text-slate-500 mb-6">Belum punya akun? <a href="<?= url('/register') ?>" class="text-violet-400 hover:underline">Daftar gratis</a></p>

        <?php if ($msg = flash_get('error')): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash_get('success')): ?>
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/login') ?>" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="text-xs text-slate-400">Alamat Email</label>
                <input type="email" name="email" required value="<?= old_input('email') ?>"
                    class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 focus:border-violet-500 outline-none text-sm"
                    placeholder="nama@email.com">
            </div>
            <div>
                <label class="text-xs text-slate-400">Password</label>
                <input type="password" name="password" required
                    class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 focus:border-violet-500 outline-none text-sm"
                    placeholder="Masukkan password">
            </div>
            <button type="submit" class="w-full py-2.5 rounded-lg btn-primary text-white text-sm font-medium">Masuk Sekarang</button>
        </form>

        <div class="mt-6 pt-4 border-t border-white/5 text-xs text-slate-500 space-y-1">
            <p>Akun contoh (password: <code class="text-slate-400">password123</code>):</p>
            <p>Admin &mdash; admin@perpus.test</p>
            <p>Member &mdash; member@perpus.test</p>
        </div>
    </div>
</div>
</body>
</html>