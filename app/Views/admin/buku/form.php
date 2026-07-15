<?php
$title = $buku ? 'Edit Buku' : 'Tambah Buku';
$action = $buku ? url('/admin/buku/' . $buku['id']) : url('/admin/buku');
?>
<div class="max-w-2xl card p-6">
    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="mb-4 px-4 py-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm space-y-1">
            <?php foreach ($_SESSION['errors'] as $err): ?><p><?= e($err) ?></p><?php endforeach; unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-slate-400">Judul Buku</label>
                <input type="text" name="judul" required value="<?= e($buku['judul'] ?? old_input('judul')) ?>"
                    class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            </div>
            <div>
                <label class="text-xs text-slate-400">Penulis</label>
                <input type="text" name="penulis" required value="<?= e($buku['penulis'] ?? old_input('penulis')) ?>"
                    class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            </div>
        </div>

        <div class="grid md:grid-cols-4 gap-4">
            <div>
                <label class="text-xs text-slate-400">Kategori</label>
                <select name="kategori_id" class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
                    <option value="">-- Tanpa Kategori --</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= (($buku['kategori_id'] ?? null) == $k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-400">Stok Fisik</label>
                <input type="number" name="stok" min="0" required value="<?= e((string) ($buku['stok'] ?? 0)) ?>"
                    class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            </div>
            <div>
                <label class="text-xs text-slate-400">Rating (0 - 5)</label>
                <input type="number" name="rating" min="0" max="5" step="0.1" value="<?= e((string) ($buku['rating'] ?? 0)) ?>"
                    class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            </div>
            <div>
                <label class="text-xs text-slate-400">Harga eBook (Rp, 0 = tidak dijual)</label>
                <input type="number" name="ebook_price" min="0" value="<?= e((string) ($buku['ebook_price'] ?? 0)) ?>"
                    class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            </div>
        </div>

        <div>
            <label class="text-xs text-slate-400">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500"><?= e($buku['deskripsi'] ?? old_input('deskripsi')) ?></textarea>
        </div>

        <div>
            <label class="text-xs text-slate-400">Cover Buku <?= $buku ? '(kosongkan jika tidak ingin mengganti)' : '' ?></label>
            <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp"
                class="w-full mt-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none file:mr-3 file:px-3 file:py-1 file:rounded file:border-0 file:bg-violet-600 file:text-white">
            <p class="text-[11px] text-slate-600 mt-1">Maksimal 2MB. Format: JPG, PNG, WEBP.</p>
            <?php if (!empty($buku['cover'])): ?>
                <img src="<?= asset('uploads/covers/' . e($buku['cover'])) ?>" class="h-24 mt-2 rounded-lg border border-white/10">
            <?php endif; ?>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-5 py-2.5 rounded-lg btn-primary text-white text-sm font-medium">Simpan</button>
            <a href="<?= url('/admin/buku') ?>" class="px-5 py-2.5 rounded-lg border border-white/10 text-sm">Batal</a>
        </div>
    </form>
</div>