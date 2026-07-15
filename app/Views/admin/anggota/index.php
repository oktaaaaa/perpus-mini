<?php $title = 'Daftar Anggota'; ?>

<div class="flex justify-end mb-5">
    <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
        class="px-5 py-2.5 rounded-lg btn-primary text-white text-sm font-medium">+ Tambah Anggota</button>
</div>

<div class="card overflow-hidden">
    <table class="w-full data text-sm">
        <thead>
            <tr class="border-b border-white/5">
                <th class="text-left px-5 py-3">Anggota</th>
                <th class="text-left px-5 py-3">Email</th>
                <th class="text-left px-5 py-3">Bergabung</th>
                <th class="text-right px-5 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($anggota)): ?>
            <tr><td colspan="4" class="text-center py-8 text-slate-500">Belum ada anggota.</td></tr>
        <?php else: foreach ($anggota as $a): ?>
            <tr class="border-b border-white/5">
                <td class="px-5 py-3 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-pink-500 flex items-center justify-center text-xs text-white font-bold">
                        <?= e(mb_substr($a['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-white"><?= e($a['name']) ?></p>
                        <p class="text-[11px] text-slate-500">Member</p>
                    </div>
                </td>
                <td class="px-5 py-3 text-slate-400"><?= e($a['email']) ?></td>
                <td class="px-5 py-3 text-slate-400"><?= tanggal_indo($a['created_at']) ?></td>
                <td class="px-5 py-3 text-right">
                    <form method="POST" action="<?= url('/admin/anggota/' . $a['id'] . '/hapus') ?>"
                        onsubmit="return confirm('Hapus anggota ini?')" class="inline">
                        <?= csrf_field() ?>
                        <button class="text-xs px-3 py-1.5 rounded-lg border border-rose-500/30 text-rose-400 hover:bg-rose-500/10">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div id="modalTambah" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 z-50">
    <div class="card p-6 w-full max-w-sm">
        <p class="text-white font-medium mb-4">Tambah Anggota</p>
        <?php if (!empty($_SESSION['errors'])): unset($_SESSION['errors']); endif; ?>
        <form method="POST" action="<?= url('/admin/anggota') ?>" class="space-y-3">
            <?= csrf_field() ?>
            <input type="text" name="name" required placeholder="Nama lengkap"
                class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            <input type="email" name="email" required placeholder="Email"
                class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            <input type="password" name="password" required minlength="6" placeholder="Password (min. 6 karakter)"
                class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            <div class="flex gap-2 pt-1">
                <button class="flex-1 py-2 rounded-lg btn-primary text-white text-sm">Simpan</button>
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="flex-1 py-2 rounded-lg border border-white/10 text-sm">Batal</button>
            </div>
        </form>
    </div>
</div>
