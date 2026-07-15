<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar | Perpus Digital</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-[#0b0b16] text-slate-200 min-h-screen flex items-center justify-center px-4">
<div class="w-full max-w-md card p-10">
    <h3 class="text-lg font-bold text-white mb-1">Daftar Gratis</h3>
    <p class="text-sm text-slate-500 mb-6">Sudah punya akun? <a href="<?= url('/login') ?>" class="text-violet-400 hover:underline">Masuk</a></p>

    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="mb-4 px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm space-y-1">
            <?php foreach ($_SESSION['errors'] as $err): ?><p><?= e($err) ?></p><?php endforeach; unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>
    <?php if ($msg = flash_get('error')): ?>
        <div class="mb-4 px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm"><?= e($msg) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= url('/register') ?>" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="text-xs text-slate-400">Nama Lengkap</label>
            <input type="text" name="name" required value="<?= old_input('name') ?>"
                class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 focus:border-violet-500 outline-none text-sm">
        </div>
        <div>
            <label class="text-xs text-slate-400">Alamat Email</label>
            <input type="email" name="email" required value="<?= old_input('email') ?>"
                class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 focus:border-violet-500 outline-none text-sm">
        </div>
        <div>
            <label class="text-xs text-slate-400">Password</label>
            <input type="password" name="password" required minlength="6"
                class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 focus:border-violet-500 outline-none text-sm">
            <p class="text-[11px] text-slate-600 mt-1">Minimal 6 karakter.</p>
        </div>
        <button type="submit" class="w-full py-2.5 rounded-lg btn-primary text-white text-sm font-medium">Daftar Gratis — Jadi Anggota</button>
    </form>
</div>
</body>
</html>