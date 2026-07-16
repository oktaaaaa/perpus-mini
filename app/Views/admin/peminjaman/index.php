<?php $title = 'Permintaan Peminjaman'; ?>

<div class="mb-5 px-4 py-3 rounded-lg bg-violet-500/10 border border-violet-500/30 text-violet-300 text-sm">
    Aturan sistem: Anggota dapat mengajukan peminjaman buku tanpa batas maksimal jumlah, selama stok tersedia.
    Saat menyetujui, Anda dapat mengatur durasi peminjaman secara fleksibel.
</div>

<div class="card overflow-hidden">
    <table class="w-full data text-sm">
        <thead>
            <tr class="border-b border-white/5">
                <th class="text-left px-5 py-3">Peminjam</th>
                <th class="text-left px-5 py-3">Buku</th>
                <th class="text-left px-5 py-3">Tgl Ajuan</th>
                <th class="text-right px-5 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($menunggu)): ?>
            <tr><td colspan="4" class="text-center py-10 text-slate-500">Tidak ada permintaan peminjaman saat ini.</td></tr>
        <?php else: foreach ($menunggu as $p): ?>
            <tr class="border-b border-white/5">
                <td class="px-5 py-3 text-white"><?= e($p['anggota_nama']) ?></td>
                <td class="px-5 py-3 text-slate-400"><?= e($p['buku_judul']) ?></td>
                <td class="px-5 py-3 text-slate-400"><?= tanggal_indo($p['created_at']) ?></td>
                <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                    <form method="POST" action="<?= url('/admin/peminjaman/' . $p['id'] . '/setujui') ?>" class="inline-flex items-center gap-1">
                        <?= csrf_field() ?>
                        <input type="number" name="lama_hari" value="<?= LAMA_PINJAM_HARI ?>" min="1" title="Lama pinjam (hari)"
                            class="w-14 px-2 py-1.5 rounded-lg bg-white/5 border border-white/10 text-xs outline-none">
                        <button class="text-xs px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20">Setujui</button>
                    </form>
                    <form method="POST" action="<?= url('/admin/peminjaman/' . $p['id'] . '/tolak') ?>"
                        onsubmit="return confirm('Tolak pengajuan ini?')" class="inline">
                        <?= csrf_field() ?>
                        <button class="text-xs px-3 py-1.5 rounded-lg border border-rose-500/30 text-rose-400 hover:bg-rose-500/10">Tolak</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
