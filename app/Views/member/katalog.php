<?php
$title = 'Katalog Buku';
use App\Core\Auth;
?>
<div class="mb-5 px-4 py-3 rounded-lg bg-violet-500/10 border border-violet-500/30 text-violet-300 text-sm">
    Anda sedang meminjam <strong><?= (int) $sedangDipinjam ?></strong> dari maksimal <strong><?= MAX_PINJAM_PER_ANGGOTA ?></strong> buku yang diizinkan.
</div>

<form method="GET" class="flex flex-col md:flex-row gap-3 mb-6">
    <input type="text" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari judul / penulis..."
        class="flex-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
    <select name="kategori_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none">
        <option value="">Semua Kategori</option>
        <?php foreach ($kategori as $k): ?>
            <option value="<?= $k['id'] ?>" <?= ($kategoriId == $k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="px-5 py-2.5 rounded-lg btn-primary text-white text-sm">Cari</button>
</form>

<?php if (empty($buku)): ?>
    <div class="card p-10 text-center text-sm text-slate-500">Tidak ada buku yang cocok dengan pencarian.</div>
<?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-5">
    <?php foreach ($buku as $b): ?>
        <div class="card overflow-hidden flex flex-col">
            <div class="h-40 bg-gradient-to-br from-violet-900/40 to-pink-900/20 flex items-center justify-center">
                <?php if (!empty($b['cover'])): ?>
                    <img src="<?= asset('uploads/covers/' . e($b['cover'])) ?>" class="h-full w-full object-cover">
                <?php else: ?><span class="text-3xl">📖</span><?php endif; ?>
            </div>
            <div class="p-3 flex-1 flex flex-col">
                <span class="text-[10px] px-2 py-0.5 rounded bg-violet-500/20 text-violet-300 w-fit"><?= e($b['kategori_nama'] ?? 'Umum') ?></span>
                <a href="<?= url('/katalog/' . $b['id']) ?>" class="text-sm font-medium text-white mt-1.5 truncate hover:text-violet-300">
                    <?= e($b['judul']) ?>
                </a>
                <p class="text-xs text-slate-500 truncate"><?= e($b['penulis']) ?></p>
                <p class="text-xs text-slate-500 mt-1">Stok: <?= (int) $b['stok'] ?></p>

                <form method="POST" action="<?= url('/pinjam/' . $b['id']) ?>" class="mt-3">
                    <?= csrf_field() ?>
                    <button type="submit" <?= $b['stok'] < 1 ? 'disabled' : '' ?>
                        class="w-full text-xs py-2 rounded-lg <?= $b['stok'] < 1 ? 'bg-white/5 text-slate-600 cursor-not-allowed' : 'btn-primary text-white' ?>">
                        <?= $b['stok'] < 1 ? 'Stok Habis' : 'Pinjam Buku' ?>
                    </button>
                </form>
                <?php if ($b['ebook_price'] > 0): ?>
                    <a href="<?= url('/ebook') ?>" class="mt-2 block text-center text-xs py-2 rounded-lg border border-white/10 hover:bg-white/5">
                        eBook — <?= rupiah($b['ebook_price']) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>