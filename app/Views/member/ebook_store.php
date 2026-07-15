<?php $title = 'eBook Store'; ?>

<div class="mb-5 px-4 py-3 rounded-lg bg-violet-500/10 border border-violet-500/30 text-violet-300 text-sm">
    Beli & baca eBook kapan saja. Unggah bukti pembayaran, admin akan memverifikasi transaksi Anda.
</div>

<?php if (empty($buku)): ?>
    <div class="card p-10 text-center text-sm text-slate-500">Belum ada eBook yang tersedia untuk dibeli.</div>
<?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-5">
    <?php foreach ($buku as $b): $sudah = in_array((int) $b['id'], $sudahBeli, true); ?>
        <div class="card overflow-hidden flex flex-col">
            <div class="h-40 bg-gradient-to-br from-violet-900/40 to-pink-900/20 flex items-center justify-center relative">
                <span class="absolute top-2 left-2 text-[10px] px-2 py-0.5 rounded bg-black/50 text-white">eBook</span>
                <?php if (!empty($b['cover'])): ?>
                    <img src="<?= asset('uploads/covers/' . e($b['cover'])) ?>" class="h-full w-full object-cover">
                <?php else: ?><span class="text-3xl">📖</span><?php endif; ?>
            </div>
            <div class="p-3 flex-1 flex flex-col">
                <p class="text-sm font-medium text-white truncate"><?= e($b['judul']) ?></p>
                <p class="text-xs text-slate-500 truncate"><?= e($b['penulis']) ?></p>
                <p class="text-sm text-violet-300 mt-1 font-semibold"><?= rupiah($b['ebook_price']) ?></p>

                <?php if ($sudah): ?>
                    <span class="mt-3 block text-center text-xs py-2 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Sudah Dibeli</span>
                <?php else: ?>
                    <button onclick="document.getElementById('modalBeli<?= $b['id'] ?>').classList.remove('hidden')"
                        class="mt-3 text-xs py-2 rounded-lg btn-primary text-white">Beli — <?= rupiah($b['ebook_price']) ?></button>
                <?php endif; ?>
            </div>
        </div>

        <div id="modalBeli<?= $b['id'] ?>" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 z-50">
            <div class="card p-6 w-full max-w-sm">
                <p class="text-white font-medium mb-1">Beli eBook</p>
                <p class="text-xs text-slate-500 mb-4"><?= e($b['judul']) ?> &mdash; <?= rupiah($b['ebook_price']) ?></p>
                <form method="POST" action="<?= url('/ebook/' . $b['id'] . '/beli') ?>" enctype="multipart/form-data" class="space-y-3">
                    <?= csrf_field() ?>
                    <div>
                        <label class="text-xs text-slate-400">Unggah Bukti Pembayaran</label>
                        <input type="file" name="bukti_bayar" required accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full mt-1 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-xs outline-none file:mr-2 file:px-2 file:py-1 file:rounded file:border-0 file:bg-violet-600 file:text-white">
                        <p class="text-[11px] text-slate-600 mt-1">Maksimal 2MB. Format: JPG, PNG, PDF.</p>
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button class="flex-1 py-2 rounded-lg btn-primary text-white text-sm">Konfirmasi Beli</button>
                        <button type="button" onclick="document.getElementById('modalBeli<?= $b['id'] ?>').classList.add('hidden')"
                            class="flex-1 py-2 rounded-lg border border-white/10 text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
