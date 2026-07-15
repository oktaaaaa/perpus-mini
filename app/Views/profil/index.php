<?php $title = 'Profil Saya'; ?>

<div class="max-w-xl card p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-xl text-white font-bold">
            <?= e(mb_substr($user['name'], 0, 1)) ?>
        </div>
        <div>
            <p class="text-white font-semibold"><?= e($user['name']) ?></p>
            <p class="text-xs text-slate-500 capitalize"><?= e($user['role']) ?> &middot; Bergabung <?= tanggal_indo($user['created_at']) ?></p>
        </div>
    </div>

    <form method="POST" action="<?= url('/profil') ?>" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="text-xs text-slate-400">Nama Lengkap</label>
            <input type="text" name="name" required value="<?= e($user['name']) ?>"
                class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
        </div>
        <div>
            <label class="text-xs text-slate-400">Alamat Email</label>
            <input type="email" name="email" required value="<?= e($user['email']) ?>"
                class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
        </div>

        <div class="pt-4 border-t border-white/5">
            <p class="text-sm font-medium text-white mb-1">Ganti Password</p>
            <p class="text-xs text-slate-500 mb-3">Kosongkan bagian ini kalau tidak ingin mengganti password.</p>

            <div class="space-y-3">
                <input type="password" name="password_saat_ini" placeholder="Password saat ini"
                    class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
                <input type="password" name="password_baru" minlength="6" placeholder="Password baru (min. 6 karakter)"
                    class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
                <input type="password" name="konfirmasi_password" placeholder="Konfirmasi password baru"
                    class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            </div>
        </div>

        <button type="submit" class="w-full py-2.5 rounded-lg btn-primary text-white text-sm font-medium">Simpan Perubahan</button>
    </form>
</div>