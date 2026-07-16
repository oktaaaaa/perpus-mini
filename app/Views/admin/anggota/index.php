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
                    <button type="button"
                        onclick="hapusAnggota(<?= (int) $a['id'] ?>, '<?= e(addslashes($a['name'])) ?>')"
                        class="text-xs px-3 py-1.5 rounded-lg border border-rose-500/30 text-rose-400 hover:bg-rose-500/10">Hapus</button>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 py-8 z-50 overflow-y-auto">
    <div class="card p-6 w-full max-w-sm max-h-[85vh] overflow-y-auto">
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

<!-- Modal Hapus Anggota -->
<div id="modalHapusAnggota" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 py-8 z-50 overflow-y-auto">
    <div class="w-full max-w-sm rounded-2xl border border-violet-500/30 p-6 max-h-[85vh] overflow-y-auto"
        style="background: linear-gradient(180deg, #1c1233 0%, #120c22 100%); box-shadow: 0 20px 60px rgba(0,0,0,0.5);">

        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
            style="background: linear-gradient(135deg, #f43f5e 0%, #ec4899 100%); box-shadow: 0 8px 24px rgba(236,72,153,0.35);">
            <i class="ti ti-user-x text-2xl text-white"></i>
        </div>

        <p class="text-white font-medium text-center mb-2">Hapus anggota ini?</p>
        <p class="text-sm text-center leading-relaxed mb-5" style="color:#b9aed6;">
            Anggota <span id="hapusNamaAnggota" class="font-semibold" style="color:#f0abfc;"></span>
            akan dihapus permanen dan tidak dapat dikembalikan.
        </p>

        <div class="flex items-start gap-2 rounded-lg px-3 py-2.5 mb-5"
            style="background: rgba(244,63,94,0.08); border: 1px solid rgba(244,63,94,0.25);">
            <i class="ti ti-alert-triangle text-sm mt-0.5" style="color:#fb7185;"></i>
            <span class="text-xs leading-relaxed" style="color:#fca5a5;">
                Anggota tidak dapat dihapus jika masih memiliki riwayat transaksi/peminjaman aktif.
            </span>
        </div>

        <div class="flex gap-2">
            <button type="button" onclick="document.getElementById('modalHapusAnggota').classList.add('hidden')"
                class="flex-1 py-2.5 rounded-lg border border-white/10 text-sm text-slate-300 hover:bg-white/5">Batal</button>
            <form method="POST" id="formHapusAnggota" class="flex-1">
                <?= csrf_field() ?>
                <button class="w-full py-2.5 rounded-lg text-white text-sm font-semibold flex items-center justify-center gap-1.5"
                    style="background: linear-gradient(135deg, #ec4899 0%, #a855f7 100%); box-shadow: 0 6px 18px rgba(236,72,153,0.35);">
                    <i class="ti ti-trash text-sm"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function hapusAnggota(id, nama) {
    document.getElementById('formHapusAnggota').action = '<?= url('/admin/anggota') ?>' + '/' + id + '/hapus';
    document.getElementById('hapusNamaAnggota').textContent = '"' + nama + '"';
    document.getElementById('modalHapusAnggota').classList.remove('hidden');
}
</script>