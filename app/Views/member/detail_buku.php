<?php $title = 'Detail Buku'; ?>

<a href="<?= url('/katalog') ?>" class="inline-flex items-center gap-1 text-sm text-slate-400 hover:text-white mb-5">&larr; Kembali ke Katalog</a>

<div class="card p-6 grid md:grid-cols-3 gap-6">
    <div class="h-72 rounded-xl overflow-hidden bg-gradient-to-br from-violet-900/40 to-pink-900/20 flex items-center justify-center">
        <?php if (!empty($buku['cover'])): ?>
            <img src="<?= asset('uploads/covers/' . e($buku['cover'])) ?>" class="h-full w-full object-cover">
        <?php else: ?><span class="text-6xl">📖</span><?php endif; ?>
    </div>

    <div class="md:col-span-2">
        <span class="text-[11px] px-2 py-0.5 rounded bg-violet-500/20 text-violet-300"><?= e($buku['kategori_nama'] ?? 'Umum') ?></span>
        <h2 class="text-2xl font-bold text-white mt-2"><?= e($buku['judul']) ?></h2>
        <p class="text-sm text-slate-400 mt-1">oleh <?= e($buku['penulis']) ?></p>

        <div class="flex gap-6 mt-4 text-sm">
            <div>
                <p class="text-slate-500 text-xs">Stok Tersedia</p>
                <p class="text-white font-semibold"><?= (int) $buku['stok'] ?> buku</p>
            </div>
            <?php if ($buku['ebook_price'] > 0): ?>
            <div>
                <p class="text-slate-500 text-xs">Harga eBook</p>
                <p class="text-white font-semibold"><?= rupiah($buku['ebook_price']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div class="mt-5">
            <p class="text-sm font-medium text-white mb-2">Sinopsis</p>
            <p class="text-sm text-slate-400 leading-relaxed">
                <?= nl2br(e($buku['deskripsi'] ?: 'Belum ada sinopsis untuk buku ini.')) ?>
            </p>
        </div>

        <div class="flex gap-3 mt-6">
            <?php if ($sudahMengajukan): ?>
                <span class="px-5 py-2.5 rounded-lg bg-white/5 text-slate-500 text-sm">Sudah Diajukan/Dipinjam</span>
            <?php elseif ($buku['stok'] < 1): ?>
                <span class="px-5 py-2.5 rounded-lg bg-white/5 text-slate-500 text-sm">Stok Habis</span>
            <?php else: ?>
                <form method="POST" action="<?= url('/pinjam/' . $buku['id']) ?>">
                    <?= csrf_field() ?>
                    <button class="px-5 py-2.5 rounded-lg btn-primary text-white text-sm font-medium">Pinjam Buku Ini</button>
                </form>
            <?php endif; ?>

            <?php if ($buku['ebook_price'] > 0): ?>
                <a href="<?= url('/ebook') ?>" class="px-5 py-2.5 rounded-lg border border-white/10 text-sm hover:bg-white/5">
                    Beli eBook &mdash; <?= rupiah($buku['ebook_price']) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>